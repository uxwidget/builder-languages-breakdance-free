#!/usr/bin/env python3
"""Fill remaining untranslated strings in a locale PO catalogue."""

from __future__ import annotations

import importlib.util
import re
import sys
import time
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

# Strings Google Translate skipped but should have Japanese UI text.
MANUAL_JA: dict[str, str] = {
    "One comment": "コメント1件",
    "Margin (Top & Bottom)": "余白（上下）",
    "User Registration Date": "ユーザー登録日",
    "Edit the template anyway - this is not an error.": "このままテンプレートを編集しても問題ありません。これはエラーではありません。",
    "Javascript": "JavaScript",
    "JavaScript": "JavaScript",
    "Four": "4",
    "Ten": "10",
    "CSS URL": "CSS URL",
    "(AND) %s for '{path}'": "（AND）'{path}' の %s",
    "%s #%s": "%s #%s",
}

# Acronyms, brands, code samples, ratios, and HTML tag labels — keep source text.
KEEP_AS_IS = {
    "REST API",
    "ACF",
    "RSS URL",
    "RDF URL",
    "RSS2 URL",
    "PHP",
    "ID",
    "CSS",
    "H1",
    "H2",
    "H3",
    "H4",
    "H5",
    "H6",
    "h1",
    "h2",
    "h3",
    "h4",
    "h5",
    "h6",
    "CTRL",
    "Google Chrome",
    "Mac OS",
    "Linux",
    "iOS (iPhone)",
    "Chrome OS",
    "RSS",
    "URL",
    "HTML",
    "SMS",
    "X",
    "Y",
    "YouTube",
    "GitHub",
    "VK",
    "RTL",
    "3DX",
    "3DXY",
    "3DY",
    "16:9",
    "16:10",
    "4:3",
    "1:1",
    "21:9",
    "3:2",
    "8:5",
    "1x",
    "UTC ",
    "2022-04-01 (Y-m-d)",
    "CC 1",
    "CC 2",
    "CC 3",
    "CCC 1",
    "CCC 2",
    "CCC 3",
    "ID %s",
    ".my-cool-class",
    ".your.css > #selector-69::after",
}


def has_japanese(text: str) -> bool:
    return any("\u3040" <= char <= "\u9fff" or "\u30a0" <= char <= "\u30ff" for char in text)


def needs_translation(entry: polib.POEntry) -> bool:
    if entry.obsolete or not entry.msgid:
        return False

    if entry.msgid in KEEP_AS_IS:
        return False

    if entry.msgid_plural:
        values = list(entry.msgstr_plural.values())
        combined = " ".join(values)
        return values == [entry.msgid, entry.msgid_plural] or not has_japanese(combined)

    msgstr = entry.msgstr or ""
    return msgstr == entry.msgid or not has_japanese(msgstr)


def translate_text(text: str, translator) -> str:
    if text in MANUAL_JA:
        return MANUAL_JA[text]

    if text in KEEP_AS_IS:
        return text

    if translator is None:
        return text

    for attempt in range(3):
        try:
            result = translator.translate(text)
            if result and result != text:
                return result
        except Exception as error:  # noqa: BLE001
            print(f"retry {attempt + 1} for {text!r}: {error}", file=sys.stderr)
            time.sleep(1.5 * (attempt + 1))

    return text


def finish_po(path: Path, translator) -> int:
    po = polib.pofile(str(path))
    updated = 0

    for entry in po:
        if not needs_translation(entry):
            continue

        if entry.msgid_plural:
            entry.msgstr_plural[0] = translate_text(entry.msgid, translator)
            entry.msgstr_plural[1] = translate_text(entry.msgid_plural, translator)
        else:
            entry.msgstr = translate_text(entry.msgid, translator)

        updated += 1
        print(f"fixed: {entry.msgid!r} -> {entry.msgstr!r}")

    po.metadata["PO-Revision-Date"] = time.strftime("%Y-%m-%d %H:%M:%z")
    po.save(str(path))
    print(f"wrote {path.name} ({updated} entries updated)")
    return updated


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    locale = sys.argv[1] if len(sys.argv) > 1 else "ja_JP"
    translator = None

    try:
        from deep_translator import GoogleTranslator

        translator = GoogleTranslator(source="en", target="ja")
    except ImportError:
        print("deep-translator not installed; using manual overrides only.", file=sys.stderr)

    total = 0
    for name in (
        f"breakdance-{locale}.po",
        f"breakdance-builder-{locale}.po",
        f"breakdance-elements-{locale}.po",
    ):
        path = LANGUAGES / name
        if not path.is_file():
            print(f"missing: {path}", file=sys.stderr)
            return 1
        total += finish_po(path, translator)

    spec = importlib.util.spec_from_file_location("gl", ROOT / "scripts" / "generate-locale.py")
    gl = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(gl)
    gl.build_json(locale)

    print(f"done: {total} entries updated for {locale}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
