#!/usr/bin/env python3
"""Append Form Builder control labels to breakdance-elements-pt_BR.po."""

from __future__ import annotations

import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
PO_PATH = ROOT / "languages" / "breakdance-elements-pt_BR.po"

ENTRIES: list[tuple[str, str, str, str]] = [
    (
        "elements/FormBuilder/element.php",
        "Control label",
        "Form Name",
        "Nome do formulário",
    ),
    (
        "elements/FormBuilder/element.php",
        "Control label",
        "Actions After Submission",
        "Ações após envio",
    ),
    (
        "elements/FormBuilder/element.php",
        "Placeholder",
        "No action selected",
        "Nenhuma ação selecionada",
    ),
    (
        "elements/FormBuilder/element.php",
        "Control label",
        "Add Field",
        "Adicionar campo",
    ),
    (
        "elements/FormBuilder/element.php",
        "Control label",
        "Error Message",
        "Mensagem de erro",
    ),
    (
        "elements/FormBuilder/element.php",
        "Control label",
        "Hide Form On Success",
        "Ocultar formulário após sucesso",
    ),
    (
        "elements/FormBuilder/element.php",
        "Control label",
        "Form HTML ID",
        "ID HTML do formulário",
    ),
    (
        "elements/FormBuilder/element.php",
        "Control label",
        "Submit HTML ID",
        "ID HTML do botão enviar",
    ),
    (
        "elements/FormBuilder/element.php",
        "Control label",
        "Add Honeypot Field",
        "Adicionar campo honeypot",
    ),
    (
        "elements/FormBuilder/element.php",
        "Control label",
        "Enable CSRF Protection",
        "Ativar proteção CSRF",
    ),
    (
        "elements/FormBuilder/element.php",
        "Control label",
        "Enable reCAPTCHA",
        "Ativar reCAPTCHA",
    ),
    (
        "plugin/forms/actions/store-submission.php",
        "Control label",
        "Store Submission",
        "Armazenar envio",
    ),
    (
        "plugin/forms/actions/store-submission.php",
        "Control label",
        "Submission Title",
        "Título do envio",
    ),
    (
        "plugin/forms/actions/store-submission.php",
        "Control label",
        "Store uploaded files",
        "Armazenar arquivos enviados",
    ),
    (
        "plugin/forms/actions/store-submission.php",
        "Control label",
        "Add uploaded files to WordPress media library",
        "Adicionar arquivos à biblioteca de mídia do WordPress",
    ),
    (
        "plugin/forms/actions/provider.php",
        "Control label",
        "Run Action Conditionally",
        "Executar ação condicionalmente",
    ),
    (
        "plugin/forms/actions/email.php",
        "Control label",
        "Emails",
        "E-mails",
    ),
    (
        "plugin/forms/actions/email.php",
        "Control label",
        "Subject",
        "Assunto",
    ),
    (
        "plugin/forms/actions/email.php",
        "Control label",
        "To Email",
        "E-mail de destino",
    ),
    (
        "plugin/forms/actions/email.php",
        "Control label",
        "From Email",
        "E-mail de origem",
    ),
    (
        "plugin/forms/actions/email.php",
        "Control label",
        "From Name",
        "Nome do remetente",
    ),
    (
        "plugin/forms/actions/email.php",
        "Control label",
        "Reply To",
        "Responder para",
    ),
    (
        "plugin/forms/actions/email.php",
        "Control label",
        "Attach uploaded files",
        "Anexar arquivos enviados",
    ),
    (
        "plugin/forms/actions/email.php",
        "Control label",
        "BCC",
        "CCO",
    ),
    (
        "elements-manual/blog-post-elements/archive-title.php",
        "Control label",
        "Disable Prefix",
        "Desativar prefixo",
    ),
    (
        "elements-manual/blog-post-elements/archive-title.php",
        "Element name",
        "Archive Title",
        "Título do arquivo",
    ),
    (
        "elements-manual/blog-post-elements/post-title.php",
        "Element name",
        "Post Title",
        "Título da postagem",
    ),
]


def entry_key(entry: polib.POEntry) -> tuple[str, str]:
    return (entry.msgctxt or "", entry.msgid)


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    po = polib.pofile(str(PO_PATH))
    existing = {entry_key(entry) for entry in po if entry.msgid and not entry.obsolete}
    added = 0

    for reference, msgctxt, msgid, msgstr in ENTRIES:
        key = (msgctxt, msgid)
        if key in existing:
            continue

        entry = polib.POEntry(msgid=msgid, msgstr=msgstr, msgctxt=msgctxt)
        entry.occurrences = [(reference, 1)]
        po.append(entry)
        existing.add(key)
        added += 1

    if added:
        po.save(str(PO_PATH))
        print(f"added {added} Form Builder entries to {PO_PATH.name}")
    else:
        print("Form Builder entries already present in pt_BR catalogue")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
