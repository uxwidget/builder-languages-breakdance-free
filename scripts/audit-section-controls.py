#!/usr/bin/env python3
"""Audit Section element control translations in pt_BR."""

from __future__ import annotations

import re
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
SECTION = (
    ROOT.parent
    / "breakdance"
    / "subplugins"
    / "breakdance-elements"
    / "elements"
    / "Section"
    / "element.php"
)
LAYOUT_V2 = ROOT.parent / "breakdance" / "subplugins" / "breakdance-elements" / "presets" / "LayoutV2.php"
PO = ROOT / "languages" / "breakdance-elements-pt_BR.po"


def extract_strings(path: Path) -> set[str]:
    text = path.read_text(encoding="utf-8", errors="ignore")
    return set(re.findall(r'["\']([A-Za-z][^"\']{1,60})["\']', text))


def lookup(po: polib.POFile, msgid: str) -> list[tuple[str, str]]:
    rows = []
    for entry in po:
        if entry.obsolete or entry.msgid != msgid:
            continue
        rows.append((entry.msgctxt or "-", entry.msgstr or ""))
    return rows


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    po = polib.pofile(str(PO))
    strings = extract_strings(SECTION) | extract_strings(LAYOUT_V2)
    interesting = sorted(
        s
        for s in strings
        if any(ch.isalpha() for ch in s)
        and not s.startswith("EssentialElements")
        and "%%" not in s
        and "design." not in s
    )

    print(f"Section-related strings ({len(interesting)}):")
    for msgid in interesting:
        rows = lookup(po, msgid)
        if not rows:
            print(f"  MISSING: {msgid}")
            continue
        for ctx, tr in rows:
            flag = "OK" if tr and tr != msgid else "EN"
            print(f"  {flag} [{ctx}] {msgid} => {tr or '(empty)'}")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
