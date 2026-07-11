#!/usr/bin/env python3
"""Bootstrap PO/JSON catalogues for locales listed in config/supported-locales.json."""

from __future__ import annotations

import argparse
import importlib.util
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SCRIPTS = ROOT / "scripts"


def load_module(name: str, filename: str):
    spec = importlib.util.spec_from_file_location(name, SCRIPTS / filename)
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)
    return module


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "--locale",
        action="append",
        dest="locales",
        help="Locale to bootstrap (repeatable). Default: missing translatable locales.",
    )
    parser.add_argument(
        "--source",
        default="en_GB",
        help="Source locale for PO cloning (default: en_GB). Use es_ES for es_LA.",
    )
    parser.add_argument(
        "--translate",
        action="store_true",
        help="Machine-translate cloned catalogues (slow).",
    )
    parser.add_argument(
        "--plugin-po",
        action="store_true",
        help="Also generate breakdance-languages plugin PO files.",
    )
    args = parser.parse_args()

    locale_config = load_module("locale_config", "locale_config.py")
    generate_locale = load_module("generate_locale", "generate-locale.py")
    languages = ROOT / "languages"

    targets = args.locales or []
    if not targets:
        for code in locale_config.translatable_locale_codes():
            elements_po = languages / f"breakdance-elements-{code}.po"
            if not elements_po.is_file():
                targets.append(code)

    if not targets:
        print("All translatable locales already have catalogues.")
        return 0

    for locale in targets:
        source = "es_ES" if locale == "es_LA" and (languages / "breakdance-es_ES.po").is_file() else args.source
        print(f"\n==> {locale} (source={source})")

        for pattern, _domain in generate_locale.PO_FILES:
            source_po = languages / pattern.format(locale=source)
            target_po = languages / pattern.format(locale=locale)
            if not source_po.is_file():
                print(f"skip missing source: {source_po.name}", file=sys.stderr)
                continue
            generate_locale.clone_po(source_po, target_po, locale, args.translate)

        generate_locale.build_json(locale)

        if args.plugin_po:
            cmd = [
                sys.executable,
                str(SCRIPTS / "sync-locale-parity.py"),
                "--plugin-only",
                "--no-translate",
            ]
            subprocess.run(cmd, cwd=str(ROOT), check=False)

    shape_cmd = [
        sys.executable,
        str(SCRIPTS / "generate-shape-divider-labels.py"),
        "--no-translate",
    ]
    for locale in targets:
        shape_cmd.extend(["--locale", locale])
    subprocess.run(shape_cmd, cwd=str(ROOT), check=False)

    print(f"\nBootstrapped {len(targets)} locale(s): {', '.join(targets)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
