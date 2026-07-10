#!/usr/bin/env python3
"""Build priority locale JSON (element names + core UI) for runtime editor overrides."""

from __future__ import annotations

import argparse
import json
import sys
import time
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"
CONFIG = ROOT / "config"

sys.path.insert(0, str(ROOT / "scripts"))
import locale_config  # noqa: E402

SOURCE_PO = LANGUAGES / "breakdance-elements-en_US.po"


def get_translator(locale: str):
    target = locale_config.translate_target(locale)

    if not target:
        return None

    try:
        from deep_translator import GoogleTranslator

        return GoogleTranslator(source="en", target=target)
    except ImportError:
        return None


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--locale", required=True)
    args = parser.parse_args()

    if not SOURCE_PO.is_file():
        print(f"missing {SOURCE_PO}", file=sys.stderr)
        return 1

    out_path = CONFIG / f"{args.locale}-priority-strings.json"
    existing = json.loads(out_path.read_text(encoding="utf-8")) if out_path.is_file() else {}
    comment = existing.pop("_comment", None)
    translator = get_translator(args.locale)
    po = polib.pofile(str(SOURCE_PO))
    added = 0

    for entry in po:
        if entry.obsolete or not entry.msgid:
            continue
        if (entry.msgctxt or "") != "Element name":
            continue
        if entry.msgid in existing:
            continue

        if translator is None:
            existing[entry.msgid] = entry.msgid
        else:
            try:
                existing[entry.msgid] = translator.translate(entry.msgid)
                time.sleep(0.05)
            except Exception as error:  # noqa: BLE001
                print(f"skip {entry.msgid!r}: {error}", file=sys.stderr)
                existing[entry.msgid] = entry.msgid

        added += 1

        if added % 20 == 0:
            print(f"  {args.locale}: {added} element names...", flush=True)

    payload = {
        "_comment": comment
        or f"Priority runtime strings for {args.locale} (element names and client-facing builder labels).",
        **existing,
    }
    out_path.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(f"wrote {out_path.name}: +{added} element names (total {len(payload) - 1})")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
