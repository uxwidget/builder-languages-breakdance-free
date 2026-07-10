#!/usr/bin/env python3
"""Apply manual glossary fixes for strings that MT could not translate."""

from __future__ import annotations

import argparse
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

GLOSSARY: dict[str, dict[str, str]] = {
    "he_IL": {
        "Font Size": "גודל גופן",
        "Product Gallery": "גלריית מוצרים",
        "Scroll Progress": "התקדמות גלילה",
        "Empty Trash": "רוקן אשפה",
        "FacetWP Facet": "FacetWP Facet",
        "Languages": "שפות",
        "Builder Language": "שפת הבילדר",
        "Builder language": "שפת הבילדר",
        "Use WordPress profile language": "השתמש בשפת פרופיל WordPress",
        "Choose the language used in the Breakdance Builder interface, element names, and first-party controls.": (
            "בחר את השפה המשמשת בממשק Breakdance Builder, בשמות האלמנטים ובפקדים המובנים."
        ),
        "Refresh": "רענון",
        "License": "רישיון",
        "Status": "סטטוס",
        "Active": "פעיל",
        "Inactive": "לא פעיל",
        "Development mode": "מצב פיתוח",
        "Buy license": "רכישת רישיון",
        "Manage license": "ניהול רישיון",
        "Atualizar": "רענון",
        "Idioma salvo com sucesso.": "השפה נשמרה בהצלחה.",
        "Não foi possível salvar o idioma selecionado.": "לא ניתן לשמור את השפה שנבחרה.",
        "Licença": "רישיון",
        "Ative sua licença para carregar as traduções do Breakdance Builder.": (
            "הפעל את הרישיון שלך כדי לטעון את תרגומי Breakdance Builder."
        ),
        "Sua licença está ativa. Todas as traduções incluídas estão disponíveis.": (
            "הרישיון שלך פעיל. כל התרגומים הכלולים זמינים."
        ),
        "Gerenciar licença": "ניהול רישיון",
        "Comprar licença": "רכישת רישיון",
    },
    "hi_IN": {
        "Languages": "भाषाएँ",
        "Builder Language": "बिल्डर भाषा",
        "Builder language": "बिल्डर भाषा",
        "Use WordPress profile language": "वर्डप्रेस प्रोफ़ाइल भाषा का उपयोग करें",
        "Choose the language used in the Breakdance Builder interface, element names, and first-party controls.": (
            "ब्रेकडांस बिल्डर इंटरफ़ेस, तत्व नामों और मूल नियंत्रणों के लिए उपयोग की जाने वाली भाषा चुनें।"
        ),
        "Refresh": "रीफ़्रेश",
        "License": "लाइसेंस",
        "Status": "स्थिति",
        "Active": "सक्रिय",
        "Inactive": "निष्क्रिय",
        "Development mode": "विकास मोड",
        "Buy license": "लाइसेंस खरीदें",
        "Manage license": "लाइसेंस प्रबंधित करें",
        "Scroll Progress": "स्क्रॉल प्रगति",
    },
}


def apply_glossary(path: Path, glossary: dict[str, str]) -> int:
    po = polib.pofile(str(path))
    changed = 0

    for entry in po:
        if entry.obsolete or not entry.msgid:
            continue

        translation = glossary.get(entry.msgid)
        if translation is None:
            continue

        if entry.msgstr != translation:
            entry.msgstr = translation
            changed += 1

    if changed:
        po.save(str(path))
        print(f"{path.name}: glossary fixed {changed}")

    return changed


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--locale", action="append", dest="locales")
    args = parser.parse_args()

    locales = args.locales or ["he_IL", "hi_IN"]
    total = 0

    for locale in locales:
        glossary = GLOSSARY.get(locale, {})
        for path in sorted(LANGUAGES.glob(f"*-{locale}.po")):
            total += apply_glossary(path, glossary)

    print(f"done: {total} glossary fixes")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
