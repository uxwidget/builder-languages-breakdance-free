#!/usr/bin/env python3
"""Run all Breakdance Languages validation checks and print a consolidated report."""

from __future__ import annotations

import importlib.util
import json
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SCRIPTS = ROOT / "scripts"
CONFIG = ROOT / "config" / "shape-divider-labels.json"


def load_script_module(name: str, filename: str):
    spec = importlib.util.spec_from_file_location(name, SCRIPTS / filename)
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)
    return module


def check_mo_files(locales: list[str], languages_dir: Path) -> list[str]:
    """Return missing .mo paths for core domains."""
    missing: list[str] = []
    domains = ("breakdance", "breakdance-builder", "breakdance-elements")

    for locale in locales:
        for domain in domains:
            mo = languages_dir / f"{domain}-{locale}.mo"
            if not mo.is_file():
                missing.append(mo.name)

    return missing


def locale_row_status(locale: str, structure_failures: list[str], placeholder_count: int) -> str:
    structure_ok = not any(failure.startswith(f"{locale}:") for failure in structure_failures)

    if locale in ("en_US", "en_GB"):
        quality = "baseline"
    elif locale in ("pt_BR", "pt_PT", "it_IT"):
        quality = "gate"
    else:
        quality = "beta"

    if not structure_ok:
        return "FAIL"
    if quality == "gate" and placeholder_count > 0:
        return "FAIL"
    if quality == "gate":
        return "PASS"
    if quality == "baseline":
        return "PASS"
    if placeholder_count <= 30:
        return "WARN"
    return "TRACK"


def check_shape_divider_labels() -> tuple[int, list[str]]:
    """Ensure locale label maps cover every Breakdance SVG preset."""
    script = SCRIPTS / "verify-shape-dividers.py"
    if not script.is_file():
        return 1, ["missing scripts/verify-shape-dividers.py"]

    import subprocess

    result = subprocess.run(
        [sys.executable, str(script)],
        cwd=str(ROOT),
        capture_output=True,
        text=True,
        encoding="utf-8",
        errors="replace",
        check=False,
    )
    output = (result.stdout or "") + (result.stderr or "")
    if result.returncode == 0:
        return 0, []

    failures = [
        line.strip()
        for line in output.splitlines()
        if line.strip().startswith("- ") or line.strip().startswith("FAILURES:")
    ]
    return result.returncode, failures or [output.strip() or "shape divider verification failed"]


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    verify = load_script_module("verify_catalogues", "verify-catalogues.py")
    qa = load_script_module("qa_placeholders", "qa-placeholders.py")

    locales = verify.LOCALES
    languages_dir = verify.LANGUAGES

    print("Breakdance Languages — validate-all")
    print("=" * 72)
    print()

    # 1. Catalogue structure
    print("[1/4] Catalogue structure (PO + JSON sync)")
    structure_code, structure_failures = verify.verify_catalogues()
    if structure_failures:
        print(f"  FAIL — {len(structure_failures)} issue(s)")
        for failure in structure_failures:
            print(f"    - {failure}")
    else:
        print("  PASS — all locales have breakdance, breakdance-builder, breakdance-elements")
    print()

    # 2. Compiled MO presence
    print("[2/4] Compiled MO files")
    missing_mo = check_mo_files(locales, languages_dir)
    if missing_mo:
        print(f"  FAIL — {len(missing_mo)} missing .mo file(s)")
        for name in missing_mo[:10]:
            print(f"    - {name}")
        if len(missing_mo) > 10:
            print(f"    ... and {len(missing_mo) - 10} more")
    else:
        print(f"  PASS — {len(locales) * 3} core .mo files present")
    print()

    # 3. Placeholder QA
    print("[3/4] Placeholder spacing QA")
    counts = qa.placeholder_counts(locales, languages_dir)
    gate_failures = [
        locale
        for locale in qa.RELEASE_GATE_LOCALES
        if counts.get(locale, 0) > 0
    ]
    if gate_failures:
        print(f"  FAIL — release gate locales with issues: {', '.join(gate_failures)}")
    else:
        print(f"  PASS — gate locales ({', '.join(qa.RELEASE_GATE_LOCALES)}) = 0")
    print()

    print("[4/4] Shape divider labels")
    shape_code, shape_failures = check_shape_divider_labels()
    if shape_failures:
        print(f"  FAIL — {len(shape_failures)} issue(s)")
        for failure in shape_failures[:10]:
            print(f"    {failure}")
    else:
        configured = []
        if CONFIG.is_file():
            configured = sorted(json.loads(CONFIG.read_text(encoding="utf-8")).keys())
        print(f"  PASS — presets covered ({', '.join(configured) or 'no locales'})")
    print()

    # Summary table
    print("Summary")
    print("-" * 72)
    print(f"{'locale':<8} | {'structure':<9} | {'placeholders':<12} | {'status':<6} | notes")
    print("-" * 72)

    for locale in locales:
        structure_ok = not any(failure.startswith(f"{locale}:") for failure in structure_failures)
        structure_label = "OK" if structure_ok else "MISSING"
        count = counts.get(locale, 0)
        status = locale_row_status(locale, structure_failures, count)

        if locale in qa.RELEASE_GATE_LOCALES:
            notes = "release gate"
        elif locale in ("en_US", "en_GB"):
            notes = "baseline"
        elif count <= 15:
            notes = "beta OK"
        elif count <= 50:
            notes = "MT residual"
        else:
            notes = "review MT"

        print(f"{locale:<8} | {structure_label:<9} | {count:<12} | {status:<6} | {notes}")

    print("-" * 72)
    print()
    print("Release gate: pt_BR, pt_PT, it_IT must have 0 placeholder issues.")
    print("Manual check: Breakdance → Idiomas → pick locale → hard refresh builder.")
    print()

    exit_code = 0
    if structure_code != 0:
        exit_code = 1
    if missing_mo:
        exit_code = 1
    if gate_failures:
        exit_code = 1
    if shape_code != 0:
        exit_code = 1

    if exit_code == 0:
        print("OVERALL: PASS")
    else:
        print("OVERALL: FAIL")

    return exit_code


if __name__ == "__main__":
    raise SystemExit(main())
