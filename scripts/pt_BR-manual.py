#!/usr/bin/env python3
"""Apply curated pt_BR translations batch-by-batch."""

from __future__ import annotations

import importlib.util
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

# Tipo 1 — Builder chrome (breakdance-builder → breakdance JSON)
BUILDER_UI: dict[str, str] = {
    "Layout": "Disposição",
    "Design": "Design",
    "Visual": "Visual",
    "Download": "Baixar",
    "Link": "Link",
    "Menu": "Menu",
    "Item": "Item",
    "Normal": "Normal",
    "Linear": "Linear",
    "Radial": "Radial",
    "Local": "Local",
    "Macros": "Macros",
    "Scripts (%s)": "Scripts (%s)",
    "Classes": "Classes",
    "Breakpoints": "Pontos de quebra",
    "History": "Histórico",
    "Choose breakpoint": "Escolha o ponto de quebra",
    "Choose breakpoints": "Escolha os pontos de quebra",
    "%s is assigned to this element but is no longer present on the site. Remove it before continuing.": (
        "%s está atribuído a este elemento, mas não existe mais no site. "
        "Remova-o antes de continuar."
    ),
}

# Tipo 2 — Element names (painel Adicionar → Básico)
ELEMENT_NAMES: dict[str, str] = {
    "Div": "Div",
    "Widget": "Widget do WordPress",
    "HTML IMG": "Imagem HTML",
    "ShortcodeWrapper": "Wrapper de shortcode",
    "Heading": "Título",
    "Section": "Seção",
    "Button": "Botão",
    "Columns": "Colunas",
    "Column": "Coluna",
    "Text": "Texto",
    "Text Link": "Link de texto",
    "Rich Text": "Texto rico",
    "Image": "Imagem",
    "Image V1": "Imagem V1",
    "Icon": "Ícone",
    "Grid": "Grade",
    "Video": "Vídeo",
    "Missing Element": "Elemento ausente",
}

# Tipo 3 — Section element + LayoutV2 preset (labels, presets, item text)
SECTION_CONTROLS: dict[str, str] = {
  # Section presets / sections
    "Layout": "Disposição",
    "Background": "Fundo",
    "Text Colors": "Cores do texto",
    "Headings": "Títulos",
    "Text": "Texto",
    "Buttons": "Botões",
    "Primary": "Primário",
    "Secondary": "Secundário",
    "Size": "Tamanho",
    "Height": "Altura",
    "Custom Height": "Altura personalizada",
    "Min Height": "Altura mínima",
    "Width": "Largura",
    "Container Width": "Largura do contêiner",
    "Spacing": "Espaçamento",
    "Padding": "Preenchimento",
    "Margin Top": "Margem superior",
    "Margin Bottom": "Margem inferior",
    "Dividers": "Divisores",
    "Shape Dividers": "Divisores de forma",
    "Borders": "Bordas",
    # LayoutV2 labels
    "Align": "Alinhar",
    "Vertical Align": "Alinhamento vertical",
    "Gap": "Espaçamento",
    "Vertical At": "Vertical em",
    "Alignment When Vertical": "Alinhamento quando vertical",
    "Items Per Row": "Itens por linha",
    "Space Between Items": "Espaço entre itens",
    "Advanced": "Avançado",
    "Item Vertical Alignment": "Alinhamento vertical do item",
    "Item Horizontal Alignment": "Alinhamento horizontal do item",
    "Display": "Exibição",
    "Flex Direction": "Direção flex",
    "Align Items": "Alinhar itens",
    "Justify Content": "Justificar conteúdo",
    "Flex Wrap": "Quebra de linha",
    "Align Content": "Alinhar conteúdo",
    "Row Gap": "Espaçamento entre linhas",
    "Text Align": "Alinhamento do texto",
    "Grid Template": "Modelo de grade",
    "Grid Auto Columns": "Colunas automáticas da grade",
    "Grid Auto Rows": "Linhas automáticas da grade",
    "Grid Auto Flow": "Fluxo automático da grade",
    "Justify Items": "Justificar itens",
    "Use Original Item Dimensions": "Usar dimensões originais do item",
    # Item text — layout modes & alignment
    "Vertical": "Vertical",
    "Horizontal": "Horizontal",
    "Grid": "Grade",
    "Left": "Esquerda",
    "Center": "Centro",
    "Right": "Direita",
    "Top": "Topo",
    "Middle": "Meio",
    "Bottom": "Inferior",
    "Space Around": "Espaço ao redor",
    "Space Between": "Espaço entre",
    "Space Evenly": "Espaço uniforme",
    "Fit Content": "Ajustar ao conteúdo",
    "Viewport": "Área visível",
    "Custom": "Personalizado",
    "Contained": "Contido",
    "Full": "Largura total",
    "Stretch": "Esticar",
    "Start": "Início",
    "End": "Fim",
    "Baseline": "Linha de base",
    "Column": "Coluna",
    "Row": "Linha",
    "Flex Start": "Início flex",
    "Flex End": "Fim flex",
    "No Wrap": "Sem quebra",
    "Wrap": "Quebra de linha",
    "Wrap Reverse": "Quebra reversa",
    "Column Reverse": "Coluna reversa",
    "Row Reverse": "Linha reversa",
    "Column Dense": "Coluna densa",
    "Row Dense": "Linha densa",
    # Lowercase flex values (legacy catalogue)
    "nowrap": "Sem quebra",
    "wrap": "Quebra de linha",
    "wrap-reverse": "Quebra reversa",
    "column-reverse": "Coluna reversa",
    "row-reverse": "Linha reversa",
}

LAYOUT_MISSING_ENTRIES: dict[tuple[str, str], str] = {
    ("Control label", "Row Gap"): "Espaçamento entre linhas",
    ("Control label", "Text Align"): "Alinhamento do texto",
    ("Control label", "Justify Items"): "Justificar itens",
    ("Control label", "Grid Template"): "Modelo de grade",
    ("Control label", "Grid Auto Columns"): "Colunas automáticas da grade",
    ("Control label", "Grid Auto Rows"): "Linhas automáticas da grade",
    ("Control label", "Grid Auto Flow"): "Fluxo automático da grade",
    ("Item text", "Flex Start"): "Início flex",
    ("Item text", "Flex End"): "Fim flex",
    ("Item text", "No Wrap"): "Sem quebra",
    ("Item text", "Wrap"): "Quebra de linha",
    ("Item text", "Wrap Reverse"): "Quebra reversa",
    ("Item text", "Column Reverse"): "Coluna reversa",
    ("Item text", "Row Reverse"): "Linha reversa",
    ("Item text", "Column Dense"): "Coluna densa",
    ("Item text", "Row Dense"): "Linha densa",
}

CONTEXT_OVERRIDES: dict[tuple[str, str], str] = {
    ("Preset section label", "Borders"): "Bordas",
    ("Control label", "Borders"): "Bordas",
    ("Control label", "Gap"): "Espaçamento",
    ("Control label", "Custom"): "Personalizado",
    ("Item text", "Custom"): "Personalizado",
    ("Item text", "Full"): "Largura total",
    ("Item text", "Start"): "Início",
    ("Control label", "Start"): "Início",
    ("Item text", "nowrap"): "Sem quebra",
    ("Item text", "wrap"): "Quebra de linha",
    ("Item text", "wrap-reverse"): "Quebra reversa",
    ("Preset section label", "Shape Dividers"): "Divisores de forma",
    ("Control label", "Margin Top"): "Margem superior",
    ("Control label", "Margin Bottom"): "Margem inferior",
    ("Control label", "Item Vertical Alignment"): "Alinhamento vertical do item",
    ("Control label", "Item Horizontal Alignment"): "Alinhamento horizontal do item",
    ("Control label", "Flex Wrap"): "Quebra de linha",
    ("Control label", "Display"): "Exibição",
}

# Tipo 4 — fancy_background / LessFancyBackground preset
BACKGROUND_CONTROLS: dict[str, str] = {
    "Background": "Fundo",
    "Color": "Cor",
    "Type": "Tipo",
    "Image": "Imagem",
    "Image Size": "Tamanho da imagem",
    "Lazy Load": "Carregamento lazy",
    "Image Settings": "Configurações da imagem",
    "Custom Position": "Posição personalizada",
    "Attachment": "Fixação",
    "Unset Image At": "Remover imagem em",
    "Gradient": "Gradiente",
    "Gradient Animation": "Animação de gradiente",
    "Scale": "Escala",
    "Speed": "Velocidade",
    "Video": "Vídeo",
    "Video Settings": "Configurações de vídeo",
    "Fallback Image": "Imagem de reserva",
    "Play On Mobile": "Reproduzir no celular",
    "No Loop": "Sem loop",
    "Pause When Out Of View": "Pausar fora da área visível",
    "YouTube Privacy Mode": "Modo de privacidade do YouTube",
    "Start Time": "Tempo inicial",
    "End Time": "Tempo final",
    "Zoom": "Ampliação",
    "Offset X": "Deslocamento X",
    "Offset Y": "Deslocamento Y",
    "Slideshow": "Apresentação de slides",
    "Slideshow Settings": "Configurações da apresentação",
    "Slide Duration": "Duração do slide",
    "Transition Effect": "Efeito de transição",
    "Effect Duration": "Duração do efeito",
    "Slide Direction": "Direção do slide",
    "Ken Burns": "Ken Burns",
    "Origin": "Origem",
    "Play Only Once": "Reproduzir apenas uma vez",
    "Overlay": "Sobreposição",
    "Opacity": "Opacidade",
    "Effects": "Efeitos",
    "Filter": "Filtro",
    "Blend Mode": "Modo de mesclagem",
    "Transition Duration": "Duração da transição",
    "Clip": "Recorte",
    "Layers": "Camadas",
    "Overlay Color": "Cor de sobreposição",
    "Repeat": "Repetição",
    "Position": "Posição",
    "Size": "Tamanho",
    "Width": "Largura",
    "Height": "Altura",
    "Left": "Esquerda",
    "Top": "Topo",
    # Item text — background type & CSS values
    "cover": "Cobrir",
    "contain": "Conter",
    "scroll": "Rolagem",
    "fixed": "Fixo",
    "Slide": "Deslizar",
    "Fade": "Desvanecer",
    "In": "Entrada",
    "Out": "Saída",
}

BACKGROUND_MISSING_ENTRIES: dict[tuple[str, str], str] = {
    ("Control label", "Clip"): "Recorte",
    ("Control label", "Layers"): "Camadas",
}

BACKGROUND_OVERRIDES: dict[tuple[str, str], str] = {
    ("Control label", "Attachment"): "Fixação",
    ("Control label", "Lazy Load"): "Carregamento lazy",
    ("Item text", "Lazy Load"): "Carregamento lazy",
    ("Control label", "Gradient Animation"): "Animação de gradiente",
    ("Control label", "Play On Mobile"): "Reproduzir no celular",
    ("Control label", "Play Only Once"): "Reproduzir apenas uma vez",
    ("Control label", "Slideshow"): "Apresentação de slides",
    ("Item text", "Slideshow"): "Apresentação de slides",
    ("Control label", "Slideshow Settings"): "Configurações da apresentação",
    ("Control label", "Transition Effect"): "Efeito de transição",
    ("Item text", "cover"): "Cobrir",
    ("Item text", "contain"): "Conter",
    ("Item text", "scroll"): "Rolagem",
    ("Control label", "Repeat"): "Repetição",
    ("Item text", "Fade"): "Desvanecer",
}

# Tipo 5 — breakdance core + border/style fixes
BREAKDANCE_CORE: dict[str, str] = {
    "Borders": "Bordas",
    "Styling": "Estilo",
    "Radius": "Raio",
    "Shadow": "Sombra",
}

BORDER_STYLE_FIXES: dict[str, str] = {
    "Double": "Duplo",
    "Inset": "Entalhe",
    "Groove": "Ranhura",
    "Ridge": "Relevo",
    "Outset": "Elevado",
}

BORDER_STYLE_OVERRIDES: dict[tuple[str, str], str] = {
    ("Item text", "Double"): "Duplo",
    ("Item text", "Inset"): "Entalhe",
}


def apply_global_map(po: polib.POFile, mapping: dict[str, str]) -> int:
    updated = 0

    for entry in po:
        if entry.obsolete or not entry.msgid or entry.msgid not in mapping:
            continue

        new_value = mapping[entry.msgid]
        if entry.msgstr == new_value:
            continue

        entry.msgstr = new_value
        updated += 1
        print(f"  {entry.msgctxt or '-'} | {entry.msgid!r} -> {new_value!r}")

    return updated


def apply_map(po: polib.POFile, mapping: dict[str, str], *, context: str | None = None) -> int:
    updated = 0

    for entry in po:
        if entry.obsolete or not entry.msgid:
            continue

        if context is not None and entry.msgctxt != context:
            continue

        if entry.msgid not in mapping:
            continue

        new_value = mapping[entry.msgid]
        if entry.msgid_plural:
            if entry.msgstr_plural.get(0) == new_value and entry.msgstr_plural.get(1) == new_value:
                continue
            entry.msgstr_plural[0] = new_value
            entry.msgstr_plural[1] = new_value
        else:
            if entry.msgstr == new_value:
                continue
            entry.msgstr = new_value

        updated += 1
        print(f"  {entry.msgctxt or '-'} | {entry.msgid!r} -> {new_value!r}")

    return updated


def add_missing_entries(
    po: polib.POFile,
    entries: dict[tuple[str, str], str],
    *,
    source: str = "presets/LayoutV2.php",
) -> int:
    added = 0
    existing = {(entry.msgctxt or "", entry.msgid) for entry in po if not entry.obsolete}

    for (context, msgid), translation in entries.items():
        key = (context, msgid)
        if key in existing:
            continue

        po.append(
            polib.POEntry(
                msgctxt=context,
                msgid=msgid,
                msgstr=translation,
                occurrences=[(source, "0")],
            )
        )
        added += 1
        print(f"  + [{context}] {msgid!r} -> {translation!r}")

    return added


def apply_context_overrides(po: polib.POFile, overrides: dict[tuple[str, str], str]) -> int:
    updated = 0

    for entry in po:
        if entry.obsolete or not entry.msgid:
            continue

        key = (entry.msgctxt or "", entry.msgid)
        if key not in overrides:
            continue

        new_value = overrides[key]
        if entry.msgstr == new_value:
            continue

        entry.msgstr = new_value
        updated += 1
        print(f"  {entry.msgctxt} | {entry.msgid!r} -> {new_value!r}")

    return updated


def rebuild_json(locale: str) -> None:
    spec = importlib.util.spec_from_file_location("gl", ROOT / "scripts" / "generate-locale.py")
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)
    module.build_json(locale)


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    batches = sys.argv[1:] or ["1", "2", "3"]
    total = 0

    builder_po = polib.pofile(str(LANGUAGES / "breakdance-builder-pt_BR.po"))
    elements_po = polib.pofile(str(LANGUAGES / "breakdance-elements-pt_BR.po"))
    core_po = polib.pofile(str(LANGUAGES / "breakdance-pt_BR.po"))

    if "1" in batches:
        print("Tipo 1 — Builder UI")
        total += apply_map(builder_po, BUILDER_UI)

    if "2" in batches:
        print("Tipo 2 — Element names")
        total += apply_map(elements_po, ELEMENT_NAMES, context="Element name")

    if "3" in batches:
        print("Tipo 3 — Section + LayoutV2 controls")
        total += add_missing_entries(elements_po, LAYOUT_MISSING_ENTRIES)
        total += apply_map(elements_po, SECTION_CONTROLS)
        total += apply_context_overrides(elements_po, CONTEXT_OVERRIDES)

    if "4" in batches:
        print("Tipo 4 — Background preset")
        total += add_missing_entries(elements_po, BACKGROUND_MISSING_ENTRIES, source="presets/background.php")
        total += apply_map(elements_po, BACKGROUND_CONTROLS)
        total += apply_context_overrides(elements_po, BACKGROUND_OVERRIDES)

    if "5" in batches:
        print("Tipo 5 — Core breakdance + border styles")
        total += apply_map(core_po, BREAKDANCE_CORE)
        total += apply_global_map(elements_po, BORDER_STYLE_FIXES)
        total += apply_context_overrides(elements_po, BORDER_STYLE_OVERRIDES)

    if total:
        builder_po.save(str(LANGUAGES / "breakdance-builder-pt_BR.po"))
        elements_po.save(str(LANGUAGES / "breakdance-elements-pt_BR.po"))
        core_po.save(str(LANGUAGES / "breakdance-pt_BR.po"))
        print(f"\nUpdated {total} entries. Rebuilding JSON + MO...")
        rebuild_json("pt_BR")

        spec = importlib.util.spec_from_file_location("cm", ROOT / "scripts" / "compile-mo.py")
        compile_mod = importlib.util.module_from_spec(spec)
        spec.loader.exec_module(compile_mod)
        compile_mod.main()
    else:
        print("No changes.")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
