#!/usr/bin/env python3
"""Audit fancy_background / LessFancyBackground translations in pt_BR."""

from __future__ import annotations

import re
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
BD = ROOT.parent / "breakdance" / "subplugins" / "breakdance-elements"
FILES = (
    BD / "elements" / "FancyBackgroundPreset" / "element.php",
    BD / "presets" / "LessFancyBackground.php",
    BD / "presets" / "background.php",
)
PO = ROOT / "languages" / "breakdance-elements-pt_BR.po"

SKIP = re.compile(
    r"^(EssentialElements|design\.|%%|https?://|breakpoint_|a_|bde-|inline|true|false|null|"
    r"scroll|fixed|repeat|cover|contain|auto|custom|normal|multiply|screen|overlay|"
    r"border-box|padding-box|content-box|youtube|vimeo|slide|fade|in|out|horizontal|vertical|"
    r"center top|left top|right top|color-dodge|hard-light|soft-light|wrap-reverse|nowrap)$",
    re.I,
)


def extract_labels(path: Path) -> set[str]:
    text = path.read_text(encoding="utf-8", errors="ignore")
    found: set[str] = set()
    for match in re.finditer(r'c\(\s*"[^"]+",\s*"([^"]+)"', text):
        label = match.group(1)
        if len(label) > 1 and not SKIP.match(label):
            found.add(label)
    for match in re.finditer(r"'text'\s*=>\s*'([^']+)'", text):
        item = match.group(1)
        if len(item) > 1 and item[0].isupper() and not SKIP.match(item):
            found.add(item)
    return found


def lookup(po: polib.POFile, msgid: str) -> list[tuple[str, str]]:
    return [
        (entry.msgctxt or "-", entry.msgstr or "")
        for entry in po
        if not entry.obsolete and entry.msgid == msgid
    ]


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    po = polib.pofile(str(PO))
    strings: set[str] = set()
    for path in FILES:
        if path.exists():
            strings |= extract_labels(path)

    print(f"Background strings ({len(strings)}):")
    missing = 0
    untranslated = 0
    for msgid in sorted(strings):
        rows = lookup(po, msgid)
        if not rows:
            print(f"  MISSING: {msgid}")
            missing += 1
            continue
        for ctx, tr in rows:
            if not tr or tr == msgid:
                print(f"  EN [{ctx}] {msgid} => {tr or '(empty)'}")
                untranslated += 1
            else:
                print(f"  OK [{ctx}] {msgid} => {tr}")

    print(f"\nSummary: {missing} missing, {untranslated} untranslated rows")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
