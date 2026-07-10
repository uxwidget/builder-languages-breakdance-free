#!/usr/bin/env python3
"""Copy translated msgstr from a source locale into a target locale when target is still English."""

from __future__ import annotations

import argparse
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

DOMAINS = (
    "breakdance-{locale}.po",
    "breakdance-builder-{locale}.po",
    "breakdance-elements-{locale}.po",
    "breakdance-languages-{locale}.po",
)


def entry_key(entry: polib.POEntry) -> tuple[str, str]:
    return (entry.msgctxt or "", entry.msgid)


def is_untranslated(entry: polib.POEntry) -> bool:
    if entry.obsolete or not entry.msgid:
        return False
    if entry.msgid_plural:
        return entry.msgstr_plural.get(0, "") in ("", entry.msgid)
    return entry.msgstr in ("", entry.msgid)


def copy_domain(source_locale: str, target_locale: str, pattern: str) -> int:
    source_path = LANGUAGES / pattern.format(locale=source_locale)
    target_path = LANGUAGES / pattern.format(locale=target_locale)

    if not source_path.is_file() or not target_path.is_file():
        print(f"skip missing: {source_path.name} -> {target_path.name}", file=sys.stderr)
        return 0

    source = polib.pofile(str(source_path))
    target = polib.pofile(str(target_path))
    source_map = {
        entry_key(entry): entry
        for entry in source
        if not entry.obsolete and entry.msgid and not is_untranslated(entry)
    }

    changed = 0
    for entry in target:
        if not is_untranslated(entry):
            continue
        donor = source_map.get(entry_key(entry))
        if donor is None:
            continue
        if entry.msgid_plural:
            entry.msgstr_plural = dict(donor.msgstr_plural)
        else:
            entry.msgstr = donor.msgstr
        changed += 1

    if changed:
        # Keep target locale headers
        target.metadata["Language"] = target_locale
        target.metadata["Language-Team"] = target.metadata.get("Language-Team", target_locale)
        target.save(str(target_path))
        print(f"{target_path.name}: copied {changed} from {source_locale}")
    else:
        print(f"{target_path.name}: nothing to copy")

    return changed


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--source", required=True)
    parser.add_argument("--target", required=True)
    parser.add_argument("--json", action="store_true")
    args = parser.parse_args()

    total = 0
    for pattern in DOMAINS:
        total += copy_domain(args.source, args.target, pattern)

    if args.json:
        import importlib.util

        spec = importlib.util.spec_from_file_location("gl", ROOT / "scripts" / "generate-locale.py")
        module = importlib.util.module_from_spec(spec)
        spec.loader.exec_module(module)
        module.build_json(args.target)

    print(f"done {args.source} -> {args.target}: {total} entries")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
