#!/usr/bin/env python3
"""Verify shape divider label maps cover every Breakdance SVG preset."""

from __future__ import annotations

import json
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CONFIG = ROOT / "config" / "shape-divider-labels.json"
SVG_DIR = (
    ROOT.parent
    / "breakdance"
    / "plugin"
    / "elements"
    / "preset-sections"
    / "shape"
    / "shape-dividers"
)


def svg_names() -> list[str]:
    if not SVG_DIR.is_dir():
        raise SystemExit(f"Missing Breakdance shape divider directory: {SVG_DIR}")

    return sorted(path.stem for path in SVG_DIR.glob("*.svg"))


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    names = svg_names()
    data = json.loads(CONFIG.read_text(encoding="utf-8"))
    failures: list[str] = []

    print(f"Breakdance SVG presets: {len(names)}")
    print(f"Locales in config: {', '.join(sorted(data))}")
    print()

    for locale, labels in sorted(data.items()):
        missing = [name for name in names if name not in labels]
        extra = [name for name in labels if name not in names]
        empty = [name for name, label in labels.items() if not str(label).strip()]

        status = "OK" if not missing and not empty else "FAIL"
        print(f"{locale}: {status} ({len(labels)} labels)")

        if missing:
            failures.extend(f"{locale}: missing label for {name}" for name in missing)
            print(f"  missing ({len(missing)}): {', '.join(missing[:8])}" + (" ..." if len(missing) > 8 else ""))
        if extra:
            print(f"  extra ({len(extra)}): {', '.join(extra[:8])}" + (" ..." if len(extra) > 8 else ""))
        if empty:
            failures.extend(f"{locale}: empty label for {name}" for name in empty)

    print()
    if failures:
        print(f"FAILURES: {len(failures)}")
        for failure in failures:
            print(f" - {failure}")
        return 1

    print("All shape divider presets have labels for every configured locale.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
