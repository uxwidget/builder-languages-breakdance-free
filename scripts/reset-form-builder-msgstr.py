#!/usr/bin/env python3
"""Reset Form Builder msgstr copied from pt_BR baseline on non-Portuguese locales."""

from __future__ import annotations

import importlib.util
import sys
from pathlib import Path

import polib

ROOT = Path(__file__).resolve().parents[1]
LANGUAGES = ROOT / "languages"

sys.path.insert(0, str(ROOT / "scripts"))
import locale_config  # noqa: E402

KEYS = {
    ("Control label", "Form Name"),
    ("Control label", "Actions After Submission"),
    ("Placeholder", "No action selected"),
    ("Control label", "Add Field"),
    ("Control label", "Error Message"),
    ("Control label", "Hide Form On Success"),
    ("Control label", "Form HTML ID"),
    ("Control label", "Submit HTML ID"),
    ("Control label", "Add Honeypot Field"),
    ("Control label", "Enable CSRF Protection"),
    ("Control label", "Enable reCAPTCHA"),
    ("Control label", "Store Submission"),
    ("Control label", "Submission Title"),
    ("Control label", "Store uploaded files"),
    ("Control label", "Add uploaded files to WordPress media library"),
    ("Control label", "Run Action Conditionally"),
    ("Control label", "Emails"),
    ("Control label", "Subject"),
    ("Control label", "To Email"),
    ("Control label", "From Email"),
    ("Control label", "From Name"),
    ("Control label", "Reply To"),
    ("Control label", "Attach uploaded files"),
    ("Control label", "BCC"),
    ("Control label", "Disable Prefix"),
    ("Element name", "Archive Title"),
    ("Element name", "Post Title"),
}


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    fixed = 0

    for locale in locale_config.translatable_locale_codes():
        if locale in ("pt_BR", "pt_PT"):
            continue

        path = LANGUAGES / f"breakdance-elements-{locale}.po"
        if not path.is_file():
            continue

        po = polib.pofile(str(path))
        changed = 0

        for entry in po:
            if entry.obsolete or not entry.msgid:
                continue
            if (entry.msgctxt or "", entry.msgid) not in KEYS:
                continue
            if entry.msgstr != entry.msgid:
                entry.msgstr = entry.msgid
                changed += 1

        if changed:
            po.save(str(path))
            spec = importlib.util.spec_from_file_location("gl", ROOT / "scripts" / "generate-locale.py")
            module = importlib.util.module_from_spec(spec)
            spec.loader.exec_module(module)
            module.build_json(locale)
            print(f"{locale}: reset {changed} Form Builder msgstr -> English")
            fixed += changed

    print(f"done ({fixed} entries reset)")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
