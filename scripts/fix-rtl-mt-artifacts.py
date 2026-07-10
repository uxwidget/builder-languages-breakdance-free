#!/usr/bin/env python3
"""Remove ZWSP and fix common MT artifacts in RTL locale PO files."""

from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

ZWSP = "\u200b"
# MT sometimes drops the type specifier: %4$word -> %4$sword
MISSING_PLACEHOLDER_TYPE = re.compile(r"%(\d+)\$(?![sd%])")


def clean_msgstr(text: str) -> str:
    if not text:
        return text

    result = text.replace(ZWSP, "")

    result = MISSING_PLACEHOLDER_TYPE.sub(r"%\1$s", result)

    # Normalize accidental double spaces left after ZWSP removal
    result = re.sub(r"  +", " ", result)
    return result


def fix_po(path: Path) -> int:
    po = polib.pofile(str(path))
    changed = 0

    for entry in po:
        if entry.obsolete or not entry.msgid:
            continue

        if entry.msgid_plural:
            for index, value in list(entry.msgstr_plural.items()):
                fixed = clean_msgstr(value)
                if fixed != value:
                    entry.msgstr_plural[index] = fixed
                    changed += 1
        else:
            fixed = clean_msgstr(entry.msgstr)
            if fixed != entry.msgstr:
                entry.msgstr = fixed
                changed += 1

    if changed:
        po.save(str(path))
        print(f"{path.name}: cleaned {changed} entries")

    return changed


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--locale", default="he_IL")
    args = parser.parse_args()

    total = 0
    for path in sorted(LANGUAGES.glob(f"*-{args.locale}.po")):
        total += fix_po(path)

    print(f"done {args.locale}: {total} entries cleaned")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
