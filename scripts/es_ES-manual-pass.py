#!/usr/bin/env python3
"""Curated es_ES fixes for high-visibility builder/admin strings."""

from __future__ import annotations

import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

BUILDER: dict[str, str] = {
    "Add a font family to %s's font family dropdowns, and optionally upload associated .woff/.woff2 files.": (
        "Agregue una familia de fuentes a los menús desplegables de familias de fuentes de %s "
        "y, opcionalmente, cargue archivos .woff/.woff2 asociados."
    ),
    "Please save your changes before closing this %s, or discard your changes.": (
        "Guarde sus cambios antes de cerrar este %s o deséchelos."
    ),
    "Merge with the existing %s.": "Fusionar con el %s existente.",
    "Error trashing existing Templates, Headers, Footers, %s, and Popups.": (
        "Error al eliminar las plantillas, encabezados, pies de página, %s y ventanas emergentes existentes."
    ),
    "Successfully trashed existing Templates, Headers, Footers, %s, and Popups.": (
        "Se eliminaron con éxito las plantillas, encabezados, pies de página, %s y ventanas emergentes existentes."
    ),
    "Trashing existing Templates, Headers, Footers, %s, and Popups.": (
        "Eliminando las plantillas, encabezados, pies de página, %s y ventanas emergentes existentes."
    ),
    "You have no published %s.": "No tienes ningún %s publicado.",
    "You have no deleted %s.": "No tienes ningún %s eliminado.",
    "Importing %s.": "Importando %s.",
    "Nothing to import for %s.": "No hay nada que importar para %s.",
    "Failed to import all %s.": "No se pudieron importar todos los %s.",
    "Successfully imported %s.": "%s se importó correctamente.",
    "The requested post can't be edited with %s.": (
        "La publicación solicitada no se puede editar con %s."
    ),
    "Please enter the %s's name.": "Introduce el nombre de %s.",
    "Create new %s": "Crear nuevo %s",
    "Export %s": "Exportar %s",
    "Rewrite %s": "Reescribir %s",
    "Upload a Video or paste a URL from %s": (
        "Sube un vídeo o pega una URL de %s."
    ),
}

BUILDER_CTXT: dict[tuple[str, str], str] = {
    ("Color", "Add %s"): "Añadir %s",
    ("Controls Tree", "Clear %s"): "Borrar %s",
    ("Controls Tree", "Paste %s"): "Pegar %s",
    ("Design Library", "Import %s"): "Importar %s",
    ("Element Presets", "Add %s"): "Añadir %s",
    ("Element Presets", "Paste %s"): "Pegar %s",
    ("Global Settings", "Import %s"): "Importar %s",
    ("Global Settings", "Add %s"): "Añadir %s",
    ("Global Block", "Add %s"): "Añadir %s",
    ("Oxygen Control", "Paste %s"): "Pegar %s",
    ("Settings", "Import %s"): "Importar %s",
    ("Template", "Add %s"): "Añadir %s",
}

ADMIN: dict[str, str] = {
    "%s is required.": "Se requiere %s.",
    "%s database has been updated successfully!": "¡La base de datos de %s se ha actualizado correctamente!",
    "Failed to delete %s.": "No se pudo eliminar %s.",
    "Improve %s": "Mejorar %s",
}


def apply_map(po_path: Path, mapping: dict[str, str], ctxt_map: dict[tuple[str, str], str]) -> int:
    po = polib.pofile(str(po_path))
    changes = 0

    for entry in po:
        if entry.obsolete or not entry.msgid:
            continue

        key = (entry.msgctxt, entry.msgid) if entry.msgctxt else None
        if key and key in ctxt_map and entry.msgstr != ctxt_map[key]:
            entry.msgstr = ctxt_map[key]
            changes += 1
            continue

        if not entry.msgctxt and entry.msgid in mapping and entry.msgstr != mapping[entry.msgid]:
            entry.msgstr = mapping[entry.msgid]
            changes += 1

    if changes:
        po.save(str(po_path))
    return changes


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    builder_path = LANGUAGES / "breakdance-builder-es_ES.po"
    admin_path = LANGUAGES / "breakdance-es_ES.po"

    builder_changes = apply_map(builder_path, BUILDER, BUILDER_CTXT)
    admin_changes = apply_map(admin_path, ADMIN, {})

    print(f"{builder_path.name}: {builder_changes} fix(es)")
    print(f"{admin_path.name}: {admin_changes} fix(es)")
    print(f"Total: {builder_changes + admin_changes}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
