#!/usr/bin/env python3
"""Compile all PO catalogues in languages/ to MO files."""

from __future__ import annotations

from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"


def main() -> int:
    count = 0

    for po_file in sorted(LANGUAGES.glob("*.po")):
        po = polib.pofile(str(po_file))
        mo_file = po_file.with_suffix(".mo")
        po.save_as_mofile(str(mo_file))
        print(f"wrote {mo_file.name}")
        count += 1

    print(f"compiled {count} mo files")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
