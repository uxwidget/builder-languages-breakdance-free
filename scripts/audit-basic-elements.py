#!/usr/bin/env python3
"""Audit basic-category element name translations."""

from __future__ import annotations

import re
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
ELEMENTS_DIR = (
    ROOT.parent
    / "breakdance"
    / "subplugins"
    / "breakdance-elements"
    / "elements"
)
PO = ROOT / "languages" / "breakdance-elements-pt_BR.po"


def basic_element_names() -> list[str]:
    names: list[str] = []

    for php in sorted(ELEMENTS_DIR.glob("*/element.php")):
        text = php.read_text(encoding="utf-8", errors="ignore")
        if "return 'basic'" not in text and 'return "basic"' not in text:
            continue

        match = re.search(
            r"static function name\(\)\s*\{.*?return\s+['\"]([^'\"]+)['\"]",
            text,
            re.S,
        )
        if match:
            names.append(match.group(1))

    return sorted(set(names))


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    po = polib.pofile(str(PO))
    translations = {
        entry.msgid: entry.msgstr
        for entry in po
        if not entry.obsolete and entry.msgid and entry.msgctxt == "Element name"
    }

    print(f"Basic elements ({len(basic_element_names())}):")
    for name in basic_element_names():
        translation = translations.get(name, "")
        if not translation:
            print(f"  NO ENTRY: {name}")
        elif translation == name:
            print(f"  UNTRANSLATED: {name}")
        else:
            print(f"  OK: {name} -> {translation}")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
