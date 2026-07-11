#!/usr/bin/env python3
"""Compare translation parity against pt_BR baseline."""

from __future__ import annotations

import importlib.util
import json
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"
CONFIG = ROOT / "config" / "shape-divider-labels.json"
BASELINE = "pt_BR"

sys.path.insert(0, str(ROOT / "scripts"))
import locale_config  # noqa: E402

LOCALES = locale_config.translatable_locale_codes() + ["en_GB"]
DOMAINS = ("breakdance", "breakdance-builder", "breakdance-elements")
PLUGIN_PO = "breakdance-languages"


def load_qa():
    spec = importlib.util.spec_from_file_location("qa", ROOT / "scripts" / "qa-placeholders.py")
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)
    return module


def po_msgids(path: Path) -> set[str]:
    if not path.is_file():
        return set()
    po = polib.pofile(str(path))
    return {entry.msgid for entry in po if not entry.obsolete and entry.msgid}


def po_stats(path: Path) -> tuple[int, int]:
    if not path.is_file():
        return 0, 0
    po = polib.pofile(str(path))
    total = 0
    translated = 0
    for entry in po:
        if entry.obsolete or not entry.msgid:
            continue
        total += 1
        if entry.msgid_plural:
            if all(entry.msgstr_plural.values()):
                translated += 1
        elif entry.msgstr:
            translated += 1
    return total, translated


def shape_labels(locale: str, data: dict) -> int:
    return len(data.get(locale, {}))


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    qa = load_qa()
    shape_data = json.loads(CONFIG.read_text(encoding="utf-8")) if CONFIG.is_file() else {}
    baseline_shape = shape_labels(BASELINE, shape_data)
    baseline_keys = {
        domain: po_msgids(LANGUAGES / f"{domain}-{BASELINE}.po") for domain in DOMAINS
    }
    baseline_total = sum(po_stats(LANGUAGES / f"{domain}-{BASELINE}.po")[0] for domain in DOMAINS)
    baseline_total += po_stats(LANGUAGES / f"{PLUGIN_PO}-{BASELINE}.po")[0]

    print(f"Baseline: {BASELINE} ({baseline_total} PO entries across core domains + plugin UI)")
    print()
    print(
        f"{'locale':<8} | {'PO fill':<8} | {'key parity':<10} | "
        f"{'plugin PO':<9} | {'shapes':<8} | {'placeholder QA':<14} | gaps"
    )
    print("-" * 88)

    failures = 0
    for locale in LOCALES + [BASELINE]:
        po_total = 0
        po_translated = 0
        missing_keys = 0
        for domain in DOMAINS:
            total, translated = po_stats(LANGUAGES / f"{domain}-{locale}.po")
            po_total += total
            po_translated += translated
            missing_keys += len(baseline_keys[domain] - po_msgids(LANGUAGES / f"{domain}-{locale}.po"))

        plugin_path = LANGUAGES / f"{PLUGIN_PO}-{locale}.po"
        plugin_total, plugin_translated = po_stats(plugin_path)
        po_total += plugin_total
        po_translated += plugin_translated

        fill = f"{po_translated}/{po_total}"
        parity = "OK" if missing_keys == 0 else f"-{missing_keys}"
        plugin = "yes" if plugin_path.is_file() else "no"
        shapes = f"{shape_labels(locale, shape_data)}/{baseline_shape}"
        placeholders = qa.placeholder_counts([locale], LANGUAGES)[locale]

        gaps: list[str] = []
        if missing_keys:
            gaps.append(f"{missing_keys} PO keys vs {BASELINE}")
        if plugin == "no":
            gaps.append("missing breakdance-languages PO")
        if shape_labels(locale, shape_data) < baseline_shape:
            gaps.append("shape divider labels")
        if placeholders and locale in ("pt_BR", "pt_PT", "it_IT"):
            gaps.append(f"{placeholders} placeholder QA")

        gap_text = "; ".join(gaps) if gaps else "—"
        if gaps and locale != BASELINE:
            failures += 1

        print(
            f"{locale:<8} | {fill:<8} | {parity:<10} | {plugin:<9} | "
            f"{shapes:<8} | {placeholders:<14} | {gap_text}"
        )

    print("-" * 88)
    print()
    print("Notes:")
    print("- PO fill = translated msgid count; MT locales may be 100% filled but need QA passes.")
    print(f"- key parity = missing msgids vs {BASELINE} in breakdance + builder + elements.")
    print("- shapes = entries in config/shape-divider-labels.json.")
    print("- editor-overrides.php today: pt_BR, pt_PT, ja_JP only (not counted here).")
    print()
    if failures:
        print(f"Locales with parity gaps vs {BASELINE}: {failures}")
        return 1
    print(f"All compared locales match {BASELINE} PO key parity.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
