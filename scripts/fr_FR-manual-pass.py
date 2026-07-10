#!/usr/bin/env python3
"""Curated fr_FR fixes for high-visibility builder/admin strings."""

from __future__ import annotations

import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

BUILDER: dict[str, str] = {
    "%s Element": "Élément %s",
    "%s failed": "Échec de %s",
    "%s Options": "Options %s",
    "%s selectors imported. %s existing selectors were kept.": (
        "Sélecteurs %s importés. %s sélecteurs existants ont été conservés."
    ),
    "%s selectors imported. All existing selectors were replaced.": (
        "Sélecteurs %s importés. Tous les sélecteurs existants ont été remplacés."
    ),
    "%s Settings, and also import all Pages, Posts, and Templates from the design set.": (
        "Paramètres %s, et importez également toutes les pages, publications et modèles "
        "de l'ensemble de conception."
    ),
    "%s variables imported. %s existing variables were kept.": (
        "Variables %s importées. %s variables existantes ont été conservées."
    ),
    "A %s with the title \"%s\" already exists. Please select another title.": (
        "Un %s portant le titre « %s » existe déjà. Veuillez sélectionner un autre titre."
    ),
    "Add a font family to %s's font family dropdowns, and optionally upload associated .woff/.woff2 files.": (
        "Ajoutez une famille de polices aux listes déroulantes des familles de polices de %s "
        "et téléchargez éventuellement les fichiers .woff/.woff2 associés."
    ),
    "Please save your changes before closing this %s, or discard your changes.": (
        "Veuillez enregistrer vos modifications avant de fermer cet %s ou ignorez vos modifications."
    ),
    "Merge with the existing %s.": "Fusionner avec le %s existant.",
    "Error trashing existing Templates, Headers, Footers, %s, and Popups.": (
        "Erreur lors de la suppression des modèles, en-têtes, pieds de page, %s "
        "et fenêtres contextuelles existants."
    ),
    "Successfully imported %s.": "%s a été importé avec succès.",
    "Successfully trashed existing Templates, Headers, Footers, %s, and Popups.": (
        "Les modèles, en-têtes, pieds de page, %s et fenêtres contextuelles existants "
        "ont été supprimés avec succès."
    ),
    "Trashing existing Templates, Headers, Footers, %s, and Popups.": (
        "Suppression des modèles, en-têtes, pieds de page, %s et fenêtres contextuelles existants."
    ),
    "You have no published %s.": "Vous n'avez aucun %s publié.",
    "You have no deleted %s.": "Vous n'avez aucun %s supprimé.",
    "Importing the \"%s\" site": "Importation du site « %s »",
    "Importing %s.": "Importation de %s.",
    "Nothing to import for %s.": "Rien à importer pour %s.",
    "Failed to import all %s.": "Échec de l'importation de tous les %s.",
    "The requested post can't be edited with %s.": (
        "La publication demandée ne peut pas être modifiée avec %s."
    ),
    "Please enter the %s's name.": "Veuillez saisir le nom de %s.",
    "Create new %s": "Créer un nouveau %s",
    "Export %s": "Exporter %s",
    "Rewrite %s": "Réécrire %s",
    "Upload a Video or paste a URL from %s": (
        "Téléversez une vidéo ou collez une URL de %s."
    ),
}

BUILDER_CTXT: dict[tuple[str, str], str] = {
    ("Color", "Add %s"): "Ajouter %s",
    ("Controls Tree", "Clear %s"): "Effacer %s",
    ("Controls Tree", "Paste %s"): "Coller %s",
    ("Design Library", "Import %s"): "Importer %s",
    ("Element Presets", "Add %s"): "Ajouter %s",
    ("Element Presets", "Paste %s"): "Coller %s",
    ("Global Settings", "Import %s"): "Importer %s",
    ("Global Settings", "Add %s"): "Ajouter %s",
    ("Global Block", "Add %s"): "Ajouter %s",
    ("Oxygen Control", "Paste %s"): "Coller %s",
    ("Settings", "Import %s"): "Importer %s",
    ("Template", "Add %s"): "Ajouter %s",
}

ADMIN: dict[str, str] = {
    "%s is required.": "%s est requis.",
    "%s database has been updated successfully!": "La base de données %s a été mise à jour avec succès !",
    "Failed to delete %s.": "Échec de la suppression de %s.",
    "Improve %s": "Améliorer %s",
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

    builder_path = LANGUAGES / "breakdance-builder-fr_FR.po"
    admin_path = LANGUAGES / "breakdance-fr_FR.po"

    builder_changes = apply_map(builder_path, BUILDER, BUILDER_CTXT)
    admin_changes = apply_map(admin_path, ADMIN, {})

    print(f"{builder_path.name}: {builder_changes} fix(es)")
    print(f"{admin_path.name}: {admin_changes} fix(es)")
    print(f"Total: {builder_changes + admin_changes}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
