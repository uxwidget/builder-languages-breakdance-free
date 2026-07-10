#!/usr/bin/env python3
"""Curated de_DE fixes for high-visibility builder/admin strings."""

from __future__ import annotations

import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

BUILDER: dict[str, str] = {
    "%s not present. Set it in the %s.": "%s nicht vorhanden. Legen Sie es im %s fest.",
    "%s. Set it in the %s.": "%s. Legen Sie es im %s fest.",
    "Are you sure you want to delete %s?": "Sind Sie sicher, dass Sie %s löschen möchten?",
    "Are you sure you want to delete the icon %s?": (
        "Sind Sie sicher, dass Sie das Symbol %s löschen möchten?"
    ),
    "Please save your changes before closing this %s, or discard your changes.": (
        "Bitte speichern Sie Ihre Änderungen, bevor Sie diesen %s schließen, "
        "oder verwerfen Sie Ihre Änderungen."
    ),
    "You've been logged out of WordPress. Log back in to continue using %s.": (
        "Sie wurden von WordPress abgemeldet. Melden Sie sich erneut an, um "
        "%s weiterhin zu verwenden."
    ),
    "Your session has expired. Refresh your session to continue using %s.": (
        "Ihre Sitzung ist abgelaufen. Aktualisieren Sie Ihre Sitzung, um "
        "%s weiterhin zu verwenden."
    ),
    "To use %s, log in to WordPress admin.": (
        "Um %s zu verwenden, melden Sie sich beim WordPress-Administrator an."
    ),
    "To use %s, you need to login to WordPress.": (
        "Um %s verwenden zu können, müssen Sie sich bei WordPress anmelden."
    ),
    # Pass 2 — import/export + error notifications
    "Add a font family to %s's font family dropdowns, and optionally upload associated .woff/.woff2 files.": (
        "Fügen Sie eine Schriftfamilie zu den Schriftfamilien-Dropdown-Menüs von %s hinzu "
        "und laden Sie optional zugehörige .woff/.woff2-Dateien hoch."
    ),
    "Add new (AND) %s": "Neues (AND) %s hinzufügen",
    "Click IMPORT to set the default properties to the properties of the currently active element. Active element must be a %s.": (
        "Klicken Sie auf IMPORTIEREN, um die Standardeigenschaften auf die Eigenschaften "
        "des aktuell aktiven Elements festzulegen. Das aktive Element muss ein %s sein."
    ),
    "Create new %s": "Neues %s erstellen",
    "Error importing %s for \"%s\"": "Fehler beim Importieren von %s für „%s“",
    "Error importing Design Presets for \"%s\"": (
        "Fehler beim Importieren von Designvoreinstellungen für „%s“"
    ),
    "Error trashing existing Templates, Headers, Footers, %s, and Popups.": (
        "Fehler beim Löschen vorhandener Vorlagen, Kopf- und Fußzeilen, %s und Popups."
    ),
    "Export %s": "%s exportieren",
    "Failed to import all %s.": "Es konnten nicht alle %s importiert werden.",
    "Importing %s.": "%s wird importiert.",
    "Merge with the existing %s.": "Mit dem vorhandenen %s zusammenführen.",
    "Nothing to import for %s.": "Für %s muss nichts importiert werden.",
    "Successfully imported %s.": "%s erfolgreich importiert.",
    "Successfully trashed existing Templates, Headers, Footers, %s, and Popups.": (
        "Vorhandene Vorlagen, Kopf- und Fußzeilen, %s und Popups wurden erfolgreich gelöscht."
    ),
    "Trashing existing Templates, Headers, Footers, %s, and Popups.": (
        "Vorhandene Vorlagen, Kopf- und Fußzeilen, %s und Popups werden gelöscht."
    ),
    "The requested post can't be edited with %s.": (
        "Der angeforderte Beitrag kann nicht mit %s bearbeitet werden."
    ),
    "Please enter the %s's name.": "Bitte geben Sie den Namen von %s ein.",
    "Rewrite %s": "%s umschreiben",
    "Upload a Video or paste a URL from %s": (
        "Laden Sie ein Video hoch oder fügen Sie eine URL von %s ein."
    ),
    "You have no deleted %s.": "Sie haben keine gelöschten %s.",
    "You have no published %s.": "Sie haben keine veröffentlichten %s.",
    "When saving in %s, the CSS and other dependencies required by the saved item are cached. Use this tool to regenerate the caches for all %s-created content.": (
        "Beim Speichern in %s werden das CSS und andere Abhängigkeiten, die für das "
        "gespeicherte Element erforderlich sind, zwischengespeichert. Verwenden Sie "
        "dieses Tool, um die Caches für alle mit %s erstellten Inhalte neu zu generieren."
    ),
}

BUILDER_CTXT: dict[tuple[str, str], str] = {
    ("Color", "Add %s"): "%s hinzufügen",
    ("Controls Tree", "Clear %s"): "%s löschen",
    ("Controls Tree", "Paste %s"): "%s einfügen",
    ("Design Library", "Import %s"): "%s importieren",
    ("Element Presets", "Add %s"): "%s hinzufügen",
    ("Element Presets", "Paste %s"): "%s einfügen",
    ("Global Settings", "Import %s"): "%s importieren",
    ("Global Settings", "Add %s"): "%s hinzufügen",
    ("Global Block", "Add %s"): "%s hinzufügen",
    ("Oxygen Control", "Paste %s"): "%s einfügen",
    ("Settings", "Import %s"): "%s importieren",
    ("Template", "Add %s"): "%s hinzufügen",
    (
        "Plugin name",
        "The template does not contain a Template Content Area element. Therefore, the posts it applies to are not editable in %s.",
    ): (
        "Die Vorlage enthält kein Element „Vorlageninhaltsbereich“. Daher können die "
        "Beiträge, auf die es sich bezieht, in %s nicht bearbeitet werden."
    ),
}

ADMIN: dict[str, str] = {
    "The <b>%1$s</b> element is only available in %2$s Pro.": (
        "Das Element <b>%1$s</b> ist nur in %2$s Pro verfügbar."
    ),
    "A Pro-only visibility condition was used on a <b>%s</b> element.": (
        "Für ein <b>%s</b>-Element wurde eine Nur-Pro-Sichtbarkeitsbedingung verwendet."
    ),
    "This page uses pro-only features. Please upgrade to <a href=\"%1$s\" target=\"_blank\">%2$s Pro</a>, or enter your <a href=\"%3$s\">license key</a>.": (
        "Diese Seite verwendet nur Pro-Funktionen. Bitte aktualisieren Sie auf "
        "<a href=\"%1$s\" target=\"_blank\">%2$s Pro</a> oder geben Sie Ihren "
        "<a href=\"%3$s\">Lizenzschlüssel</a> ein."
    ),
    "Failed to delete %s.": "%s konnte nicht gelöscht werden.",
    "%s database has been updated successfully!": "Die %s-Datenbank wurde erfolgreich aktualisiert!",
    "The <b>\"%1$s\"</b> element can only be added to the <b>WooCommerce %2$s Page</b>.": (
        "Das Element <b>\"%1$s\"</b> kann nur zur Seite <b>WooCommerce %2$s Page</b> "
        "hinzugefügt werden."
    ),
    "Set the <b>WooCommerce %s Page</b> in the WP admin at <b>WooCommerce &gt; Settings &gt; Advanced &gt; Page Setup</b>.": (
        "Legen Sie die <b>WooCommerce %s Page</b> im WP-Administrator unter "
        "<b>WooCommerce &gt; Einstellungen &gt; Erweitert &gt; Seiteneinrichtung</b> fest."
    ),
    "No page has been set as the <b>WooCommerce %s Page</b>.": (
        "Es wurde keine Seite als <b>WooCommerce %s Page</b> festgelegt."
    ),
    "The <b>WooCommerce %1$s Page</b> is currently set to <b>%2$s (ID: %3$d)</b>.": (
        "Die <b>WooCommerce %1$s Page</b> ist derzeit auf <b>%2$s (ID: %3$d)</b> eingestellt."
    ),
    # Pass 2 — errors / permissions / template inheritance
    "Improve %s": "%s verbessern",
    "Yes, I want to help improve %s": "Ja, ich möchte helfen, %s zu verbessern",
    "Required POST parameters are missing or invalid: \"%s\"": (
        "Erforderliche POST-Parameter fehlen oder sind ungültig: „%s“"
    ),
    "But template <em>%1$s (ID %2$d)</em> tries to inherit its design from template %3$d.": (
        "Aber Vorlage <em>%1$s (ID %2$d)</em> versucht, sein Design von Vorlage %3$d zu erben."
    ),
    "By default, %1$s does not apply %2$s filter to %1$s-designed content. You can enable this option to make %1$s run %3$s on singular content created with %1$s, but you should understand the %4$spotential security implications%5$s.": (
        "Standardmäßig wendet %1$s den Filter %2$s nicht auf von %1$s entworfene Inhalte an. "
        "Sie können diese Option aktivieren, damit %1$s %3$s für einzelne Inhalte ausführt, "
        "die mit %1$s erstellt wurden. Sie sollten jedoch die %4$spotenziellen "
        "Sicherheitsauswirkungen%5$s verstehen."
    ),
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

        if not entry.msgctxt and entry.msgid in mapping:
            new = mapping[entry.msgid]
            if entry.msgid_plural:
                for index in entry.msgstr_plural:
                    if entry.msgstr_plural[index] != new:
                        entry.msgstr_plural[index] = new
                        changes += 1
            elif entry.msgstr != new:
                entry.msgstr = new
                changes += 1

    if changes:
        po.save(str(po_path))
    return changes


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    builder_path = LANGUAGES / "breakdance-builder-de_DE.po"
    admin_path = LANGUAGES / "breakdance-de_DE.po"

    builder_changes = apply_map(builder_path, BUILDER, BUILDER_CTXT)
    admin_changes = apply_map(admin_path, ADMIN, {})

    print(f"{builder_path.name}: {builder_changes} fix(es)")
    print(f"{admin_path.name}: {admin_changes} fix(es)")
    print(f"Total: {builder_changes + admin_changes}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
