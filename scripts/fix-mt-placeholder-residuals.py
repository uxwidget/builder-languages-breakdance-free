#!/usr/bin/env python3
"""Fix real MT placeholder residuals (corruption, glue, quote spacing).

Does not force Latin spacing rules onto CJK. Complements fix-placeholder-spacing.py.
"""

from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

# MT sometimes "translates" the letter s inside %s
CORRUPT_PCT_S = [
    (re.compile(r"%에스"), "%s"),  # Korean "S"
    (re.compile(r"%エス"), "%s"),  # Japanese katakana "S"
    (re.compile(r"%ｓ"), "%s"),  # fullwidth s
    (re.compile(r"%Ｓ"), "%s"),
    (re.compile(r"%д\b"), "%d"),  # Cyrillic lookalike rare
]

# MT drops the type letter: %4$potential -> should be %4$s (link wrapper)
MISSING_TYPE = re.compile(r"%(\d+)\$(?![sd%])")

# Glued "%ssupprimé" style when msgid has a space after the placeholder
GLUED_WORD_AFTER = re.compile(r"(%(?:\d+\$)?[sd])(?=[A-Za-zÀ-ÖØ-öø-ÿ])")

# Space inside opening typographic/straight quotes before placeholder
SPACE_IN_OPEN_QUOTE = re.compile(
    r'([\"“„«『「‹])\s+(?=%(?:\d+\$)?[sd])'
)
# Space inside closing quotes after placeholder (%s / %1$s)
SPACE_IN_CLOSE_QUOTE = re.compile(
    r"(%(?:\d+\$)?[sd])\s+([\"”»』」›])"
)

# Spurious " %s -Word" when msgid has "%s Word" or "%s-Word" without space-hyphen
SPACE_BEFORE_HYPHEN = re.compile(
    r"(?<!%)(%(?:\d+\$)?[sd])\s+([–—-])"
)

# Arabic letter glued to placeholder (missing space)
AR_GLUE = re.compile(
    r"([\u0600-\u06FF])(%(?:\d+\$)?[sd])|(%(?:\d+\$)?[sd])([\u0600-\u06FF])"
)

# Latin letter glued when msgid has a space on that side
PLACEHOLDER = re.compile(r"(?<!%)%(?!%)(?:\d+\$)?[sd]")
GLUED_LATIN_AFTER = re.compile(r"(?<!%)(%(?:\d+\$)?[sd])(?=[A-Za-zÀ-ÖØ-öø-ÿ])")
GLUED_LATIN_BEFORE = re.compile(r"(?<=[A-Za-zÀ-ÖØ-öø-ÿ])(?<!%)(%(?:\d+\$)?[sd])")


def source_compact(msgid: str, placeholder: str) -> bool:
    escaped = re.escape(placeholder)
    return bool(
        re.search(escaped + r"(?=[A-Za-z])", msgid or "")
        or re.search(r"(?<=[A-Za-z])" + escaped, msgid or "")
    )


def fix_string(msgid: str, msgstr: str) -> str:
    if not msgstr:
        return msgstr

    result = msgstr

    for pattern, repl in CORRUPT_PCT_S:
        result = pattern.sub(repl, result)

    result = MISSING_TYPE.sub(r"%\1$s", result)

    result = SPACE_IN_OPEN_QUOTE.sub(r"\1", result)
    result = SPACE_IN_CLOSE_QUOTE.sub(r"\1\2", result)

    # Only strip space-hyphen if msgid does not use " %s -"
    if not re.search(r"%(?:\d+\$)?[sd]\s+[-–—]", msgid or ""):
        result = SPACE_BEFORE_HYPHEN.sub(r"\1\2", result)

    # Arabic glue: always insert space (Arabic needs separation from Latin tokens)
    def _ar_space(match: re.Match[str]) -> str:
        if match.group(1) is not None:
            return f"{match.group(1)} {match.group(2)}"
        return f"{match.group(3)} {match.group(4)}"

    result = AR_GLUE.sub(_ar_space, result)

    # Latin glue: insert space after placeholder when msgstr glues a Latin word
    # and the English source does not intentionally compact that placeholder.
    pieces: list[str] = []
    last = 0
    for match in PLACEHOLDER.finditer(result):
        pieces.append(result[last : match.end()])
        after = result[match.end() : match.end() + 1]
        if (
            after
            and after.isalpha()
            and after.isascii()
            and not source_compact(msgid, match.group(0))
        ):
            pieces.append(" ")
        last = match.end()
    pieces.append(result[last:])
    result = "".join(pieces)

    if msgid:
        msgid_matches = list(PLACEHOLDER.finditer(msgid))
        if msgid_matches:
            if any(
                m.start() > 0 and msgid[m.start() - 1].isspace() for m in msgid_matches
            ):
                result = GLUED_LATIN_BEFORE.sub(r" \1", result)

    result = re.sub(r"  +", " ", result)
    return result


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
        "--locale",
        action="append",
        dest="locales",
        help="Locale to fix (repeatable). Default: all non-baseline product locales.",
    )
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()

    sys.path.insert(0, str(ROOT / "scripts"))
    import locale_config  # noqa: E402

    locales = args.locales or [
        code
        for code in locale_config.locale_codes()
        if code not in ("en_GB",)
    ]

    total = 0
    for locale in locales:
        for po_path in sorted(LANGUAGES.glob(f"*-{locale}.po")):
            n = fix_po(po_path, args.dry_run)
            if n:
                suffix = " (dry-run)" if args.dry_run else ""
                print(f"{po_path.name}: fixed {n} entr(y/ies){suffix}")
                total += n

    action = "Would fix" if args.dry_run else "Fixed"
    print(f"\n{action} {total} entr(y/ies) total.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
