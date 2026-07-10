#!/usr/bin/env python3
"""Find suspicious spacing around gettext placeholders in PO files."""

from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

sys.path.insert(0, str(ROOT / "scripts"))
import locale_config  # noqa: E402

SUPPORTED_LOCALES = locale_config.locale_codes()
RELEASE_GATE_LOCALES = locale_config.release_gate_locales()

PLACEHOLDER = r"(?<!%)%(?!%)(?:\d+\$)?[sd]"

# Typographic quotes + CJK/fullwidth punctuation + compounds are valid flush against placeholders.
OK_AFTER = (
    r"\s<,.;:!?…)\]\}/'\"“”„‚«»『』「」‹›。"
    r"、！？：；）】》〉，．％\-–—"
)
OK_BEFORE = (
    r"\s(<>\[{/='\"“”„‚«»『』「」‹›（【《〈¡¿°"
    r"、，．\-–—"
)

BAD_AFTER = re.compile(PLACEHOLDER + rf"(?=[^{OK_AFTER}])")
BAD_BEFORE = re.compile(rf"(?<=[^{OK_BEFORE}])" + PLACEHOLDER)
UNIT_AFTER = re.compile(PLACEHOLDER + r"(?=(?:px|em|rem|vh|vw|%)\b)", re.IGNORECASE)

# Hangul / Hiragana / Katakana / CJK Unified / Devanagari — normal to sit next to %s
CJK_CHAR = re.compile(
    r"[\u0900-\u097F\u1100-\u11FF\u3040-\u30FF\u3400-\u4DBF\u4E00-\u9FFF\uAC00-\uD7AF]"
)


def source_uses_compact_placeholder(source: str, placeholder: str) -> bool:
    """Return true when the source intentionally glues this placeholder to text."""
    escaped = re.escape(placeholder)
    return bool(
        re.search(escaped + r"(?=[A-Za-z])", source)
        or re.search(r"(?<=[A-Za-z])" + escaped, source)
    )


def adjacent_is_cjk(value: str, index: int, side: str) -> bool:
    """CJK scripts correctly omit spaces around placeholders."""
    if side == "after":
        if index >= len(value):
            return False
        return bool(CJK_CHAR.match(value[index]))
    if index <= 0:
        return False
    return bool(CJK_CHAR.match(value[index - 1]))


def scan_po(path: Path) -> list[tuple[int, str, str]]:
    problems: list[tuple[int, str, str]] = []
    po = polib.pofile(str(path))

    for entry in po:
        if entry.obsolete:
            continue

        values: list[str]
        if entry.msgid_plural:
            values = [value for _, value in sorted(entry.msgstr_plural.items())]
        else:
            values = [entry.msgstr]

        for value in values:
            if not value:
                continue

            suspicious_after = [
                match
                for match in BAD_AFTER.finditer(value)
                if not UNIT_AFTER.match(value, match.start())
                and not source_uses_compact_placeholder(entry.msgid, match.group(0))
                and not adjacent_is_cjk(value, match.end(), "after")
            ]
            suspicious_before = [
                match
                for match in BAD_BEFORE.finditer(value)
                if value[match.start() - 1 : match.start()] != "#"
                and not source_uses_compact_placeholder(entry.msgid, match.group(0))
                and not adjacent_is_cjk(value, match.start(), "before")
            ]

            if suspicious_after or suspicious_before:
                problems.append((entry.linenum, entry.msgid, value))

    return problems


def placeholder_counts(
    locales: list[str] | None = None,
    languages_dir: Path | None = None,
) -> dict[str, int]:
    """Return suspicious placeholder counts keyed by locale code."""
    locales = locales or SUPPORTED_LOCALES
    languages_dir = languages_dir or LANGUAGES
    counts: dict[str, int] = {}

    for locale in locales:
        files = sorted(languages_dir.glob(f"*-{locale}.po"))
        counts[locale] = sum(len(scan_po(path)) for path in files)

    return counts


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
        default="pt_*",
        help="PO locale glob segment to scan, for example pt_* or fr_FR. Default: pt_*.",
    )
    parser.add_argument(
        "--summary",
        action="store_true",
        help="Print one line per locale (requires --locale for each, or pass --all-supported).",
    )
    parser.add_argument(
        "--all-supported",
        action="store_true",
        help="With --summary, scan the release locales from verify-catalogues.py.",
    )
    args = parser.parse_args()

    if args.all_supported and args.summary:
        locales = SUPPORTED_LOCALES
        print("locale | suspicious")
        print("-" * 24)
        exit_code = 0
        for locale, count in placeholder_counts(locales, Path(args.path)).items():
            print(f"{locale} | {count}")
            if locale in RELEASE_GATE_LOCALES and count:
                exit_code = 1
        return exit_code

    target = Path(args.path)
    files = [target] if target.is_file() else sorted(target.glob(f"*{args.locale}.po"))
    total = 0

    for po_path in files:
        problems = scan_po(po_path)
        if not problems:
            continue

        total += len(problems)
        print(f"\n{po_path.name}: {len(problems)} suspicious placeholder spacing issue(s)")
        for line, msgid, msgstr in problems:
            print(f"  line {line}: msgid={msgid!r}")
            print(f"           msgstr={msgstr!r}")

    if total:
        print(f"\nTotal: {total} suspicious issue(s)")
        return 1

    print("No suspicious placeholder spacing issues found.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
