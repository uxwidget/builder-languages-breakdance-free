#!/usr/bin/env python3
"""Expand hi_IN priority JSON with all Element name labels from the elements PO."""

from __future__ import annotations

import json
import sys
import time
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"
PRIORITY_PATH = ROOT / "config" / "hi_IN-priority-strings.json"
SOURCE_PO = LANGUAGES / "breakdance-elements-en_GB.po"


def get_translator():
    try:
        from deep_translator import GoogleTranslator

        return GoogleTranslator(source="en", target="hi")
    except ImportError:
        return None


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    if not SOURCE_PO.is_file():
        print(f"missing {SOURCE_PO}", file=sys.stderr)
        return 1

    data = json.loads(PRIORITY_PATH.read_text(encoding="utf-8")) if PRIORITY_PATH.is_file() else {}
    comment = data.pop("_comment", None)
    translator = get_translator()
    po = polib.pofile(str(SOURCE_PO))
    added = 0

    for entry in po:
        if entry.obsolete or not entry.msgid:
            continue
        if (entry.msgctxt or "") != "Element name":
            continue
        if entry.msgid in data:
            continue

        if translator is None:
            data[entry.msgid] = entry.msgid
        else:
            try:
                data[entry.msgid] = translator.translate(entry.msgid)
                time.sleep(0.05)
            except Exception as error:  # noqa: BLE001
                print(f"skip {entry.msgid!r}: {error}", file=sys.stderr)
                data[entry.msgid] = entry.msgid

        added += 1

        if added % 20 == 0:
            print(f"  element names: {added}...", flush=True)

    if comment:
        data = {"_comment": comment, **data}
    else:
        data = {
            "_comment": "Client-facing Hindi strings for builder chrome and element names.",
            **data,
        }

    PRIORITY_PATH.write_text(json.dumps(data, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(f"wrote {PRIORITY_PATH.name}: +{added} element names (total {len(data) - 1})")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
