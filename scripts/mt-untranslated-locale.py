#!/usr/bin/env python3
"""Machine-translate untranslated PO entries (msgstr == msgid) for one locale."""

from __future__ import annotations

import argparse
import sys
import time
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

sys.path.insert(0, str(ROOT / "scripts"))
import locale_config  # noqa: E402

DOMAINS = (
    "breakdance-{locale}.po",
    "breakdance-builder-{locale}.po",
    "breakdance-elements-{locale}.po",
)


def get_translator(locale: str):
    target = locale_config.translate_target(locale)

    if not target:
        return None

    try:
        from deep_translator import GoogleTranslator

        return GoogleTranslator(source="en", target=target)
    except ImportError:
        return None


def needs_translation(entry: polib.POEntry) -> bool:
    if entry.obsolete or not entry.msgid:
        return False

    if entry.msgid_plural:
        return any(
            (entry.msgstr_plural.get(index, "") or "") in ("", entry.msgid, entry.msgid_plural)
            for index in sorted(entry.msgstr_plural.keys())
        )

    msgstr = entry.msgstr or ""
    return msgstr == "" or msgstr == entry.msgid


def translate_entry(translator, entry: polib.POEntry) -> bool:
    try:
        if entry.msgid_plural:
            entry.msgstr_plural[0] = translator.translate(entry.msgid)
            entry.msgstr_plural[1] = translator.translate(entry.msgid_plural)
        else:
            entry.msgstr = translator.translate(entry.msgid)
        return True
    except Exception as error:  # noqa: BLE001
        print(f"skip: {error}", file=sys.stderr)
        return False


def mt_po(path: Path, locale: str, limit: int = 0) -> tuple[int, int]:
    translator = get_translator(locale)

    if translator is None:
        print(f"{path.name}: no translator for {locale}", file=sys.stderr)
        return 0, 0

    po = polib.pofile(str(path))
    translated = 0
    scanned = 0

    for index, entry in enumerate(po):
        if not needs_translation(entry):
            continue

        scanned += 1

        if translate_entry(translator, entry):
            translated += 1

        if translated and translated % 25 == 0:
            print(f"  {path.name}: {translated} translated...", flush=True)
            time.sleep(0.15)

        if limit and translated >= limit:
            break

        if index and index % 100 == 0:
            time.sleep(0.05)

    if translated:
        po.save(str(path))
        print(f"{path.name}: translated {translated} entries ({scanned} pending scanned)")

    return translated, scanned


def rebuild_json(locale: str) -> None:
    import importlib.util

    spec = importlib.util.spec_from_file_location("gl", ROOT / "scripts" / "generate-locale.py")
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)
    module.build_json(locale)


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--locale", required=True)
    parser.add_argument(
        "--limit",
        type=int,
        default=0,
        help="Max entries to translate per PO file (0 = all).",
    )
    parser.add_argument("--json", action="store_true", help="Rebuild JSON after MT.")
    args = parser.parse_args()

    total = 0

    for pattern in DOMAINS:
        path = LANGUAGES / pattern.format(locale=args.locale)

        if not path.is_file():
            print(f"missing {path.name}", file=sys.stderr)
            continue

        count, _pending = mt_po(path, args.locale, args.limit)
        total += count

    if args.json and total:
        rebuild_json(args.locale)

    print(f"done {args.locale}: {total} entries machine-translated")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
