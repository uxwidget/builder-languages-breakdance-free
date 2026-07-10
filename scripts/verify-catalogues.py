#!/usr/bin/env python3
"""Verify Breakdance Languages catalogues for every supported locale."""

from __future__ import annotations

import importlib.util
import json
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

sys.path.insert(0, str(ROOT / "scripts"))
import locale_config  # noqa: E402

LOCALES = locale_config.locale_codes()
PO_DOMAINS = ("breakdance", "breakdance-builder", "breakdance-elements")


def load_generate_locale():
    spec = importlib.util.spec_from_file_location("gl", ROOT / "scripts" / "generate-locale.py")
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)
    return module


def po_keys(path: Path) -> set[str]:
    po = polib.pofile(str(path))
    gl = load_generate_locale()
    return {gl.jed_key(entry) for entry in po if not entry.obsolete and entry.msgid}


def json_keys(path: Path, domain: str) -> set[str]:
    data = json.loads(path.read_text(encoding="utf-8"))
    return {key for key in data.get("locale_data", {}).get(domain, {}) if key}


def verify_catalogues() -> tuple[int, list[str]]:
    """Return exit code and failure messages."""
    failures: list[str] = []

    for locale in LOCALES:
        for domain in PO_DOMAINS:
            path = LANGUAGES / f"{domain}-{locale}.po"
            if not path.is_file():
                failures.append(f"{locale}: missing {path.name}")

        builder_json = LANGUAGES / f"breakdance-{locale}.json"
        elements_json = LANGUAGES / f"breakdance-elements-{locale}.json"

        if not builder_json.is_file():
            failures.append(f"{locale}: missing {builder_json.name}")
        else:
            expected = po_keys(LANGUAGES / f"breakdance-builder-{locale}.po")
            actual = json_keys(builder_json, "breakdance")
            if expected != actual:
                failures.append(
                    f"{locale}: breakdance-{locale}.json key mismatch "
                    f"(po={len(expected)} json={len(actual)} missing={len(expected - actual)})"
                )

        if not elements_json.is_file():
            failures.append(f"{locale}: missing {elements_json.name}")
        else:
            expected = po_keys(LANGUAGES / f"breakdance-elements-{locale}.po")
            actual = json_keys(elements_json, "breakdance-elements")
            if expected != actual:
                failures.append(
                    f"{locale}: breakdance-elements-{locale}.json key mismatch "
                    f"(po={len(expected)} json={len(actual)} missing={len(expected - actual)})"
                )

    return (1 if failures else 0, failures)


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    failures: list[str] = []

    print("locale | breakdance.po | builder.po | elements.po | builder.json | elements.json")
    print("-" * 88)

    for locale in LOCALES:
        po_status = []
        for domain in PO_DOMAINS:
            path = LANGUAGES / f"{domain}-{locale}.po"
            if not path.is_file():
                failures.append(f"{locale}: missing {path.name}")
                po_status.append("MISSING")
            else:
                po_status.append("OK")

        builder_json = LANGUAGES / f"breakdance-{locale}.json"
        elements_json = LANGUAGES / f"breakdance-elements-{locale}.json"

        builder_json_status = "OK"
        elements_json_status = "OK"

        if not builder_json.is_file():
            failures.append(f"{locale}: missing {builder_json.name}")
            builder_json_status = "MISSING"
        else:
            expected = po_keys(LANGUAGES / f"breakdance-builder-{locale}.po")
            actual = json_keys(builder_json, "breakdance")
            if expected != actual:
                failures.append(
                    f"{locale}: breakdance-{locale}.json key mismatch "
                    f"(po={len(expected)} json={len(actual)} missing={len(expected - actual)})"
                )
                builder_json_status = "MISMATCH"

        if not elements_json.is_file():
            failures.append(f"{locale}: missing {elements_json.name}")
            elements_json_status = "MISSING"
        else:
            expected = po_keys(LANGUAGES / f"breakdance-elements-{locale}.po")
            actual = json_keys(elements_json, "breakdance-elements")
            if expected != actual:
                failures.append(
                    f"{locale}: breakdance-elements-{locale}.json key mismatch "
                    f"(po={len(expected)} json={len(actual)} missing={len(expected - actual)})"
                )
                elements_json_status = "MISMATCH"

        print(
            f"{locale} | {po_status[0]} | {po_status[1]} | {po_status[2]} | "
            f"{builder_json_status} | {elements_json_status}"
        )

    print()
    if failures:
        print(f"FAILURES: {len(failures)}")
        for failure in failures:
            print(f" - {failure}")
        return 1

    print("All locales have breakdance, breakdance-builder, and breakdance-elements catalogues.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
