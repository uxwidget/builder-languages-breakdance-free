#!/usr/bin/env python3
"""Sync pt_BR baseline parity: element PO keys + plugin UI PO files."""

from __future__ import annotations

import argparse
import re
import sys
import time
from copy import deepcopy
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"
UI_STRINGS = ROOT / "includes" / "ui-strings.php"
BASELINE = "pt_BR"

sys.path.insert(0, str(ROOT / "scripts"))
import locale_config  # noqa: E402

ELEMENT_LOCALES = [
    code
    for code in locale_config.translatable_locale_codes()
    if code != BASELINE
]
PLUGIN_LOCALES = [
    code
    for code in ELEMENT_LOCALES
    if code != "it_IT"
]

PLUGIN_META = {
    locale: locale_config.plugin_meta(locale)
    for locale in PLUGIN_LOCALES
}

TRANSLATE_TARGETS = {
    locale: target
    for locale in PLUGIN_LOCALES
    if (target := locale_config.translate_target(locale))
}

# msgid in breakdance-languages-pt_BR.po -> ui-strings.php key (en_US value resolved at runtime)
PLUGIN_UI_KEYS: dict[str, str] = {
    "Languages": "tab_title",
    "Builder Language": "header_alt",
    "Builder language": "builder_language_label",
    "Choose the language used in the Breakdance Builder interface, element names, and first-party controls.": "page_description",
    "Use WordPress profile language": "auto",
    "Atualizar": "refresh",
    "Idioma salvo com sucesso.": "success",
    "Salvando idioma...": "loading",
    "Não foi possível salvar o idioma selecionado.": "error",
    "Licença": "license_heading",
    "Status": "license_status",
    "Development mode": "license_status_dev",
    "Active": "license_status_active",
    "Inactive": "license_status_inactive",
    "Build de desenvolvimento: licenciamento Freemius não configurado.": "license_dev_description",
    "Ative sua licença para carregar as traduções do Breakdance Builder.": "license_inactive_description",
    "Sua licença está ativa. Todas as traduções incluídas estão disponíveis.": "license_active_description",
    "Conta": "license_account",
    "Gerenciar licença": "license_manage",
    "Comprar licença": "license_buy",
    "Ative uma licença válida para usar as traduções do Breakdance Languages.": "license_notice",
}

# English source for legacy Portuguese msgids or mixed catalogue entries.
PLUGIN_EN_SOURCES: dict[str, str] = {
    "Idioma salvo com sucesso. Agora clique em \"Atualizar\" para recarregar o Breakdance e aplicar a tradução.": (
        'Language saved successfully. Click "Refresh" to reload the page and apply the translation.'
    ),
    "Siga os passos:": "Follow these steps:",
    "1) Clique em \"Salvar novo idioma\".": '1) Click "Save language".',
    "2) Clique em \"Atualizar\".": '2) Click "Refresh".',
    "Salvar novo idioma": "Save language",
    "Idioma salvo. Agora clique em \"Atualizar\".": 'Language saved. Now click "Refresh".',
    "Comprar": "Buy",
}

LANGUAGE_LABEL_MSGIDS = set(
    (locale_config.load_registry().get("locales") or {}).get(code, {}).get("label", code)
    for code in locale_config.locale_codes()
)


def entry_key(entry: polib.POEntry) -> tuple[str, str]:
    return (entry.msgctxt or "", entry.msgid)


def get_translator(locale: str):
    try:
        from deep_translator import GoogleTranslator

        return GoogleTranslator(source="en", target=TRANSLATE_TARGETS[locale])
    except ImportError:
        return None


def translate_text(translator, text: str) -> str:
    if not text or translator is None:
        return text
    try:
        return translator.translate(text)
    except Exception as error:  # noqa: BLE001
        print(f"translate skip: {error}", file=sys.stderr)
        return text


def parse_ui_catalog(locale: str) -> dict[str, str]:
    source = UI_STRINGS.read_text(encoding="utf-8")
    pattern = rf"'{re.escape(locale)}'\s*=>\s*\[(.*?)\n\s*\],"
    match = re.search(pattern, source, re.DOTALL)
    if not match:
        return {}
    block = match.group(1)
    return dict(re.findall(r"'([^']+)'\s*=>\s*'((?:\\'|[^'])*)'", block))


def parse_label_catalog(locale: str) -> dict[str, str]:
    source = UI_STRINGS.read_text(encoding="utf-8")
    fn_start = source.find("function breakdance_languages_locale_labels_catalog")
    fn_block = source[fn_start:]
    pattern = rf"'{re.escape(locale)}'\s*=>\s*\[(.*?)\n\s*\],"
    match = re.search(pattern, fn_block, re.DOTALL)
    if not match:
        return {}
    block = match.group(1)
    return dict(re.findall(r"'([^']+)'\s*=>\s*'((?:\\'|[^'])*)'", block))


def sync_element_keys(locale: str, translate: bool) -> int:
    baseline_path = LANGUAGES / f"breakdance-elements-{BASELINE}.po"
    target_path = LANGUAGES / f"breakdance-elements-{locale}.po"
    baseline = polib.pofile(str(baseline_path))
    target = polib.pofile(str(target_path))
    existing = {entry_key(entry) for entry in target if entry.msgid and not entry.obsolete}
    translator = get_translator(locale) if translate else None
    added = 0

    for entry in baseline:
        if entry.obsolete or not entry.msgid:
            continue
        key = entry_key(entry)
        if key in existing:
            continue

        new_entry = deepcopy(entry)
        if translate and translator is not None:
            if new_entry.msgid_plural:
                new_entry.msgstr_plural[0] = translate_text(translator, new_entry.msgid)
                new_entry.msgstr_plural[1] = translate_text(translator, new_entry.msgid_plural)
            else:
                new_entry.msgstr = translate_text(translator, new_entry.msgid)
            time.sleep(0.05)
        elif locale not in (BASELINE, "pt_PT"):
            if new_entry.msgid_plural:
                new_entry.msgstr_plural[0] = new_entry.msgid
                new_entry.msgstr_plural[1] = new_entry.msgid_plural
            else:
                new_entry.msgstr = new_entry.msgid
        target.append(new_entry)
        existing.add(key)
        added += 1

    if added:
        target.save(str(target_path))
        print(f"elements {locale}: added {added} keys -> {target_path.name}")
    else:
        print(f"elements {locale}: already in parity")
    return added


def plugin_translation_for_msgid(
    msgid: str,
    locale: str,
    ui_catalog: dict[str, str],
    label_catalog: dict[str, str],
    translator,
) -> str:
    if msgid in PLUGIN_UI_KEYS:
        ui_key = PLUGIN_UI_KEYS[msgid]
        if ui_key == "auto":
            value = label_catalog.get("auto")
        else:
            value = ui_catalog.get(ui_key)
        if value:
            return value

    if msgid in LANGUAGE_LABEL_MSGIDS:
        code_map = {
            meta.get("label", code): code
            for code, meta in (locale_config.load_registry().get("locales") or {}).items()
            if isinstance(meta, dict)
        }
        code = code_map.get(msgid)
        if code and code in label_catalog:
            return label_catalog[code]

    english = PLUGIN_EN_SOURCES.get(msgid, msgid)
    return translate_text(translator, english)


def sync_plugin_po(locale: str, translate: bool) -> int:
    template_path = LANGUAGES / f"breakdance-languages-{BASELINE}.po"
    target_path = LANGUAGES / f"breakdance-languages-{locale}.po"
    template = polib.pofile(str(template_path))
    team, language, plural = PLUGIN_META[locale]
    target = polib.POFile()
    target.metadata = {
        "Project-Id-Version": "Breakdance Languages ux-0.1.0",
        "Language-Team": team,
        "Language": language,
        "MIME-Version": "1.0",
        "Content-Type": "text/plain; charset=UTF-8",
        "Content-Transfer-Encoding": "8bit",
        "Plural-Forms": plural,
        "X-Domain": "breakdance-languages",
    }

    ui_catalog = parse_ui_catalog(locale)
    label_catalog = parse_label_catalog(locale)
    translator = get_translator(locale) if translate else None
    count = 0

    for entry in template:
        if entry.obsolete or not entry.msgid:
            continue
        new_entry = polib.POEntry(msgid=entry.msgid)
        if translate:
            new_entry.msgstr = plugin_translation_for_msgid(
                entry.msgid,
                locale,
                ui_catalog,
                label_catalog,
                translator,
            )
            time.sleep(0.05)
        else:
            new_entry.msgstr = entry.msgid
        target.append(new_entry)
        count += 1

    target.save(str(target_path))
    print(f"plugin {locale}: wrote {count} entries -> {target_path.name}")
    return count


def rebuild_json_for_locales(locales: list[str]) -> None:
    spec_path = ROOT / "scripts" / "generate-locale.py"
    import importlib.util

    spec = importlib.util.spec_from_file_location("generate_locale", spec_path)
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)
    for locale in locales:
        module.build_json(locale)


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "--elements-only",
        action="store_true",
        help="Only sync the 18 missing breakdance-elements keys.",
    )
    parser.add_argument(
        "--plugin-only",
        action="store_true",
        help="Only regenerate breakdance-languages plugin PO files.",
    )
    parser.add_argument(
        "--no-translate",
        action="store_true",
        help="Copy English msgstr instead of machine translation.",
    )
    parser.add_argument(
        "--json",
        action="store_true",
        help="Rebuild JSON catalogues after element sync.",
    )
    args = parser.parse_args()
    translate = not args.no_translate

    element_locales = [] if args.plugin_only else ELEMENT_LOCALES
    plugin_locales = [] if args.elements_only else PLUGIN_LOCALES

    for locale in element_locales:
        sync_element_keys(locale, translate)

    for locale in plugin_locales:
        sync_plugin_po(locale, translate)

    if args.json and element_locales:
        rebuild_json_for_locales(element_locales)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
