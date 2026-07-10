#!/usr/bin/env python3
"""Generate a new locale from an existing Breakdance Languages catalogue."""

from __future__ import annotations

import argparse
import json
import re
import sys
import time
from copy import deepcopy
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

sys.path.insert(0, str(ROOT / "scripts"))
import locale_config  # noqa: E402

LOCALE_META = {
    code: {
        "language_team": meta.get("language_team", code),
        "language": code,
        "plural_forms": meta.get("plural_forms", "nplurals=2; plural=(n != 1);"),
    }
    for code, meta in (locale_config.load_registry().get("locales") or {}).items()
    if isinstance(meta, dict)
}

TRANSLATE_TARGETS = {
    code: target
    for code in locale_config.translatable_locale_codes()
    if (target := locale_config.translate_target(code))
}

PO_FILES = (
    ("breakdance-{locale}.po", "breakdance"),
    ("breakdance-builder-{locale}.po", "breakdance"),
    ("breakdance-elements-{locale}.po", "breakdance-elements"),
)


def configure_headers(po: polib.POFile, locale: str) -> None:
    meta = LOCALE_META.get(locale, {})
    po.metadata["Language"] = meta.get("language", locale)
    po.metadata["Language-Team"] = meta.get("language_team", locale)
    po.metadata["Plural-Forms"] = meta.get("plural_forms", "nplurals=2; plural=(n != 1);")
    po.metadata["PO-Revision-Date"] = time.strftime("%Y-%m-%d %H:%M:%z")


def clone_po(source: Path, target: Path, locale: str, translate: bool) -> None:
    po = polib.pofile(str(source))
    configure_headers(po, locale)

    translator = None
    if translate:
        try:
            from deep_translator import GoogleTranslator

            target_code = TRANSLATE_TARGETS.get(locale, locale.split("_", 1)[0])
            translator = GoogleTranslator(source="en", target=target_code)
        except ImportError:
            print("deep-translator not installed; copying English strings.", file=sys.stderr)

    for index, entry in enumerate(po):
        if entry.obsolete or not entry.msgid:
            continue

        if not translate or translator is None:
            if entry.msgid_plural:
                entry.msgstr_plural[0] = entry.msgid
                entry.msgstr_plural[1] = entry.msgid_plural
            else:
                entry.msgstr = entry.msgid
            continue

        try:
            if entry.msgid_plural:
                entry.msgstr_plural[0] = translator.translate(entry.msgid)
                entry.msgstr_plural[1] = translator.translate(entry.msgid_plural)
            else:
                entry.msgstr = translator.translate(entry.msgid)
        except Exception as error:  # noqa: BLE001
            print(f"translate skip #{index}: {error}", file=sys.stderr, flush=True)
            if entry.msgid_plural:
                entry.msgstr_plural[0] = entry.msgid
                entry.msgstr_plural[1] = entry.msgid_plural
            else:
                entry.msgstr = entry.msgid

        if index and index % 100 == 0:
            print(f"translated {index} entries...", flush=True)
            time.sleep(0.2)

    po.save(str(target))
    print(f"wrote {target.name}", flush=True)


def jed_key(entry: polib.POEntry) -> str:
    """Build a WordPress JED lookup key, including gettext context when present."""
    if entry.msgctxt:
        return f"{entry.msgctxt}\x04{entry.msgid}"
    return entry.msgid


def po_entries_to_jed(po: polib.POFile, domain: str, locale: str) -> dict:
    locale_data: dict[str, object] = {
        "": {
            "domain": domain,
            "lang": locale.replace("_", "-"),
            "plural-forms": po.metadata.get("Plural-Forms", "nplurals=2; plural=(n != 1);"),
        }
    }

    for entry in po:
        if entry.obsolete or not entry.msgid:
            continue

        key = jed_key(entry)

        if entry.msgid_plural:
            translations = [
                entry.msgstr_plural.get(index, "")
                for index in sorted(entry.msgstr_plural.keys())
            ]
        else:
            translations = [entry.msgstr or entry.msgid]

        locale_data[key] = translations

    return {
        "translation-revision-date": po.metadata.get("PO-Revision-Date", ""),
        "generator": "Breakdance Languages",
        "domain": domain,
        "locale_data": {domain: locale_data},
    }


def merge_jed(base: dict, extra: dict, domain: str) -> dict:
    merged = deepcopy(base)
    extra_entries = extra.get("locale_data", {}).get(domain, {})
    merged.setdefault("locale_data", {}).setdefault(domain, {})
    merged["locale_data"][domain].update(extra_entries)
    return merged


def build_json(locale: str) -> None:
    builder_po = LANGUAGES / f"breakdance-builder-{locale}.po"
    elements_po = LANGUAGES / f"breakdance-elements-{locale}.po"

    builder_jed = po_entries_to_jed(polib.pofile(str(builder_po)), "breakdance", locale)

    breakdance_json = LANGUAGES / f"breakdance-{locale}.json"
    elements_json = LANGUAGES / f"breakdance-elements-{locale}.json"

    breakdance_json.write_text(
        json.dumps(builder_jed, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )
    elements_jed = po_entries_to_jed(
        polib.pofile(str(elements_po)),
        "breakdance-elements",
        locale,
    )
    elements_json.write_text(
        json.dumps(elements_jed, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )
    print(f"wrote {breakdance_json.name}")
    print(f"wrote {elements_json.name}")


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--source", default="en_US")
    parser.add_argument("--target", required=True)
    parser.add_argument(
        "--translate",
        action="store_true",
        help="Machine-translate strings with deep-translator (slow).",
    )
    parser.add_argument(
        "--json-only",
        action="store_true",
        help="Rebuild JSON catalogues from existing PO files.",
    )
    args = parser.parse_args()

    if args.json_only:
        build_json(args.target)
        return 0

    for pattern, _domain in PO_FILES:
        source = LANGUAGES / pattern.format(locale=args.source)
        target = LANGUAGES / pattern.format(locale=args.target)
        if not source.is_file():
            print(f"missing source: {source}", file=sys.stderr)
            return 1
        clone_po(source, target, args.target, args.translate)

    build_json(args.target)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
