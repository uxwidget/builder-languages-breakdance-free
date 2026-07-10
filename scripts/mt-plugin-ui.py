#!/usr/bin/env python3
"""Machine-translate breakdance-languages-{locale}.po plugin UI catalogues."""

from __future__ import annotations

import argparse
import sys
import time
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

sys.path.insert(0, str(ROOT / "scripts"))
import locale_config  # noqa: E402

# Portuguese msgids that appear in the template — map to English first, then translate.
PT_TO_EN = {
    "Atualizar": "Refresh",
    "Idioma salvo com sucesso.": "Language saved successfully.",
    "Não foi possível salvar o idioma selecionado.": "Could not save the selected language.",
    "Licença": "License",
    "Ative sua licença para carregar as traduções do Breakdance Builder.": (
        "Activate your license to load Breakdance Builder translations."
    ),
    "Sua licença está ativa. Todas as traduções incluídas estão disponíveis.": (
        "Your license is active. All included translations are available."
    ),
    "Gerenciar licença": "Manage license",
    "Comprar licença": "Buy license",
    "Siga os passos:": "Follow these steps:",
    '1) Clique em "Salvar novo idioma".': '1) Click "Save language".',
    '2) Clique em "Atualizar".': '2) Click "Refresh".',
    "Salvar novo idioma": "Save language",
    'Idioma salvo. Agora clique em "Atualizar".': 'Language saved. Now click "Refresh".',
    "Comprar": "Buy",
    'Idioma salvo com sucesso. Agora clique em "Atualizar" para recarregar o Breakdance e aplicar a tradução.': (
        'Language saved successfully. Click "Refresh" to reload the page and apply the translation.'
    ),
}


def get_translator(locale: str):
    target = locale_config.translate_target(locale)
    if not target:
        return None
    try:
        from deep_translator import GoogleTranslator

        return GoogleTranslator(source="en", target=target)
    except ImportError:
        return None


def english_source(msgid: str) -> str:
    return PT_TO_EN.get(msgid, msgid)


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--locale", action="append", dest="locales", required=True)
    args = parser.parse_args()

    for locale in args.locales:
        path = LANGUAGES / f"breakdance-languages-{locale}.po"
        if not path.is_file():
            print(f"missing {path.name}", file=sys.stderr)
            continue

        translator = get_translator(locale)
        if translator is None:
            print(f"{locale}: no translator", file=sys.stderr)
            continue

        po = polib.pofile(str(path))
        changed = 0
        for entry in po:
            if entry.obsolete or not entry.msgid:
                continue
            if entry.msgstr and entry.msgstr != entry.msgid and entry.msgstr not in PT_TO_EN:
                # Already translated to something other than English/Portuguese source
                if entry.msgstr != english_source(entry.msgid):
                    continue
            source = english_source(entry.msgid)
            try:
                entry.msgstr = translator.translate(source)
                changed += 1
                time.sleep(0.05)
            except Exception as error:  # noqa: BLE001
                print(f"skip {entry.msgid!r}: {error}", file=sys.stderr)
                entry.msgstr = source

        po.save(str(path))
        print(f"{path.name}: translated {changed} entries")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
