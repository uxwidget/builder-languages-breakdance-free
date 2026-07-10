#!/usr/bin/env python3
"""Generate shape divider display labels for all supported locales."""

from __future__ import annotations

import argparse
import json
import re
import sys
import time
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CONFIG = ROOT / "config" / "shape-divider-labels.json"

sys.path.insert(0, str(ROOT / "scripts"))
import locale_config  # noqa: E402
SVG_DIR = (
    ROOT.parent
    / "breakdance"
    / "plugin"
    / "elements"
    / "preset-sections"
    / "shape"
    / "shape-dividers"
)

TARGET_LOCALES = [
    code
    for code in locale_config.translatable_locale_codes()
    if code != "pt_BR"
]

TRANSLATE_TARGETS = {
    code: target
    for code in TARGET_LOCALES
    if (target := locale_config.translate_target(code))
}


def svg_names() -> list[str]:
    if not SVG_DIR.is_dir():
        raise SystemExit(f"Missing Breakdance shape divider directory: {SVG_DIR}")
    return sorted(path.stem for path in SVG_DIR.glob("*.svg"))


def english_label(key: str) -> str:
    spaced = re.sub(r"([a-z])([A-Z])", r"\1 \2", key)
    return re.sub(r"(\D)(\d+)", r"\1 \2", spaced).strip()


def get_translator(locale: str):
    try:
        from deep_translator import GoogleTranslator

        return GoogleTranslator(source="en", target=TRANSLATE_TARGETS[locale])
    except ImportError:
        return None


def translate_label(translator, text: str) -> str:
    if translator is None:
        return text
    try:
        return translator.translate(text)
    except Exception as error:  # noqa: BLE001
        print(f"translate skip ({text!r}): {error}", file=sys.stderr)
        return text


def generate_locale_labels(locale: str, names: list[str], translate: bool) -> dict[str, str]:
    translator = get_translator(locale) if translate else None
    labels: dict[str, str] = {}

    for index, name in enumerate(names, start=1):
        source = english_label(name)
        labels[name] = translate_label(translator, source)
        if translate and translator is not None:
            time.sleep(0.05)
        if index % 10 == 0:
            print(f"  {locale}: {index}/{len(names)}", flush=True)

    return labels


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "--locale",
        action="append",
        dest="locales",
        help="Locale to generate (repeatable). Default: all missing targets.",
    )
    parser.add_argument(
        "--force",
        action="store_true",
        help="Regenerate even when the locale already has 53 labels.",
    )
    parser.add_argument(
        "--no-translate",
        action="store_true",
        help="Use English labels instead of machine translation.",
    )
    args = parser.parse_args()

    names = svg_names()
    data = json.loads(CONFIG.read_text(encoding="utf-8")) if CONFIG.is_file() else {}
    data.setdefault("pt_BR", {})

    locales = args.locales or TARGET_LOCALES
    translate = not args.no_translate

    for locale in locales:
        existing = data.get(locale, {})
        if not args.force and len(existing) >= len(names):
            print(f"{locale}: already has {len(existing)} labels (skip)")
            continue

        print(f"{locale}: generating {len(names)} labels...")
        data[locale] = generate_locale_labels(locale, names, translate)

    CONFIG.write_text(json.dumps(data, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(f"wrote {CONFIG.name} ({len(data)} locales)")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
