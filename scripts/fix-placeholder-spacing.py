#!/usr/bin/env python3
"""Repair machine-translation placeholder spacing using the English msgid as reference."""

from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path

import polib

PLACEHOLDER = re.compile(r"(?<!%)%(?!%)(?:\d+\$)?[sd]")
BRAND_PRO = re.compile(r"(?<!%)%(\d+\$)?sPro\b")
GLUED_AFTER = re.compile(r"(?<!%)%(?:\d+\$)?[sd](?=[A-Za-zÀ-ÖØ-öø-ÿ])")
GLUED_BEFORE = re.compile(r"(?<=[A-Za-zÀ-ÖØ-öø-ÿ])(?<!%)(%(?:\d+\$)?[sd])")


def fix_glued_placeholders(msgid: str, msgstr: str) -> str:
    if not msgid or not msgstr:
        return msgstr

    msgid_matches = list(PLACEHOLDER.finditer(msgid))
    msgstr_matches = list(PLACEHOLDER.finditer(msgstr))
    if len(msgid_matches) != len(msgstr_matches):
        return msgstr

    result = msgstr

    if any(match.end() < len(msgid) and msgid[match.end()].isspace() for match in msgid_matches):
        result = GLUED_AFTER.sub(lambda match: match.group(0) + " ", result)

    if any(match.start() > 0 and msgid[match.start() - 1].isspace() for match in msgid_matches):
        result = GLUED_BEFORE.sub(r" \1", result)

    return result


def fix_string(msgid: str, msgstr: str) -> str:
    if not msgid or not msgstr:
        return msgstr

    result = msgstr

    if re.search(r"(?<!%)%(?:\d+\$)?s Pro", msgid):
        result = BRAND_PRO.sub(r"%\1s Pro", result)

    msgid_matches = list(PLACEHOLDER.finditer(msgid))
    msgstr_matches = list(PLACEHOLDER.finditer(result))

    if len(msgid_matches) != len(msgstr_matches):
        return result

    insertions: list[tuple[int, str]] = []

    for mid, mstr in zip(msgid_matches, msgstr_matches):
        start = mstr.start()
        end = mstr.end()

        if mid.start() > 0 and msgid[mid.start() - 1].isspace():
            if start > 0 and not result[start - 1].isspace() and result[start - 1] not in "(<[{":
                insertions.append((start, " "))

        if mid.end() < len(msgid) and msgid[mid.end()].isspace():
            if end < len(result) and not result[end].isspace() and result[end] not in ".,;:!?)>]}'\"":
                insertions.append((end, " "))

    for pos, char in sorted(insertions, key=lambda item: item[0], reverse=True):
        result = result[:pos] + char + result[pos:]

    return fix_glued_placeholders(msgid, result)


def fix_entry(entry: polib.POEntry) -> bool:
    changed = False

    if entry.msgid_plural:
        for index, value in list(entry.msgstr_plural.items()):
            fixed = fix_string(entry.msgid, value)
            if fixed != value:
                entry.msgstr_plural[index] = fixed
                changed = True
        return changed

    fixed = fix_string(entry.msgid, entry.msgstr)
    if fixed != entry.msgstr:
        entry.msgstr = fixed
        changed = True

    return changed


def fix_po(path: Path, dry_run: bool) -> int:
    po = polib.pofile(str(path))
    changes = 0

    for entry in po:
        if entry.obsolete or not entry.msgid:
            continue
        if fix_entry(entry):
            changes += 1

    if changes and not dry_run:
        po.save(str(path))

    return changes


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "path",
        nargs="?",
        default=str(Path(__file__).resolve().parents[1] / "languages"),
        help="Directory containing PO files or a single PO file.",
    )
    parser.add_argument(
        "--locale",
        action="append",
        dest="locales",
        help="Locale code to fix (repeatable). Default: fr_FR de_DE es_ES ar ja_JP.",
    )
    parser.add_argument("--dry-run", action="store_true", help="Report changes without saving.")
    args = parser.parse_args()

    locales = args.locales or ["fr_FR", "de_DE", "es_ES", "ar", "ja_JP"]
    target = Path(args.path)
    total = 0

    for locale in locales:
        files = (
            [target]
            if target.is_file()
            else sorted(target.glob(f"*-{locale}.po"))
        )
        for po_path in files:
            changes = fix_po(po_path, args.dry_run)
            if changes:
                total += changes
                suffix = " (dry-run)" if args.dry_run else ""
                print(f"{po_path.name}: fixed {changes} entr(y/ies){suffix}")

    if total == 0:
        print("No placeholder spacing fixes applied.")
        return 0

    action = "Would fix" if args.dry_run else "Fixed"
    print(f"\n{action} {total} entr(y/ies) total.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
