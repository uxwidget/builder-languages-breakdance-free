#!/usr/bin/env python3
"""Build a commercial ZIP for Builder Languages for Breakdance.

Respects `.distignore` (gitignore-like patterns) so cache, scripts, secrets and
marketing files never land in the upload package by accident.

Usage:
  python scripts/pack-release.py
  python scripts/pack-release.py --dry-run
  python scripts/pack-release.py --list-excluded
  python scripts/pack-release.py --out dist/custom.zip
  python scripts/pack-release.py --freemius-config path/to/production-freemius.php

Output default: dist/builder-languages-breakdance-{version}.zip
ZIP root folder: builder-languages-breakdance/
"""

from __future__ import annotations

import argparse
import fnmatch
import re
import sys
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
DISTIGNORE = ROOT / ".distignore"
PLUGIN_FILE = ROOT / "builder-languages-breakdance.php"
PLUGIN_SLUG = "builder-languages-breakdance"
DIST_DIR = ROOT / "dist"

# Always excluded even if missing from .distignore (safety net).
HARD_EXCLUDES = (
    ".git",
    ".git/**",
    ".blb-secret",
    ".blb-secret.*",
    "config/blb-secret.local",
    "config/freemius.php",
    "dist",
    "dist/**",
    "__pycache__",
    "**/__pycache__/**",
    "*.pyc",
    ".DS_Store",
    "Thumbs.db",
)


def plugin_version() -> str:
    source = PLUGIN_FILE.read_text(encoding="utf-8")
    match = re.search(r"^\s*\*\s*Version:\s*(.+)$", source, re.MULTILINE)
    if not match:
        raise SystemExit(f"Version header not found in {PLUGIN_FILE.name}")
    return match.group(1).strip()


def load_distignore_patterns() -> list[str]:
    patterns: list[str] = list(HARD_EXCLUDES)
    if not DISTIGNORE.is_file():
        print(f"warning: {DISTIGNORE.name} missing — using hard excludes only", file=sys.stderr)
        return patterns

    for raw in DISTIGNORE.read_text(encoding="utf-8").splitlines():
        line = raw.strip()
        if not line or line.startswith("#"):
            continue
        patterns.append(line)
    return patterns


def normalize_rel(path: Path) -> str:
    return path.as_posix()


def pattern_to_regex(pattern: str) -> re.Pattern[str]:
    """Convert a single gitignore-ish pattern to a regex matched against posix rel paths."""
    anchored = pattern.startswith("/")
    if anchored:
        pattern = pattern[1:]

    dir_only = pattern.endswith("/")
    if dir_only:
        pattern = pattern[:-1]

    # Escape regex meta, then restore glob tokens.
    escaped = re.escape(pattern)
    escaped = escaped.replace(r"\*\*", "§§GLOBSTAR§§")
    escaped = escaped.replace(r"\*", "[^/]*")
    escaped = escaped.replace(r"\?", "[^/]")
    escaped = escaped.replace("§§GLOBSTAR§§", ".*")

    if anchored:
        regex = f"^{escaped}"
    else:
        regex = f"(^|/){escaped}"

    if dir_only:
        regex += r"(?:/.*)?$"
    else:
        regex += r"(?:$|/.*)"

    return re.compile(regex)


def compile_rules(patterns: list[str]) -> list[re.Pattern[str]]:
    return [pattern_to_regex(p) for p in patterns]


def is_excluded(rel: str, rules: list[re.Pattern[str]]) -> bool:
    # Also honor simple basename globs like *.pyc via fnmatch for belt-and-suspenders.
    name = Path(rel).name
    if name.endswith(".pyc") or name in {".DS_Store", "Thumbs.db"}:
        return True
    if name == "__pycache__" or "/__pycache__/" in f"/{rel}/":
        return True

    for rule in rules:
        if rule.search(rel):
            return True

    # Extra fnmatch for unanchored wildcards that regex might miss on odd patterns.
    for pattern in HARD_EXCLUDES:
        p = pattern[1:] if pattern.startswith("/") else pattern
        if fnmatch.fnmatch(rel, p) or fnmatch.fnmatch(name, p):
            return True

    return False


def iter_pack_files(rules: list[re.Pattern[str]]) -> tuple[list[Path], list[str]]:
    included: list[Path] = []
    excluded: list[str] = []

    for path in ROOT.rglob("*"):
        if not path.is_file():
            continue
        rel = normalize_rel(path.relative_to(ROOT))
        if is_excluded(rel, rules):
            excluded.append(rel)
            continue
        included.append(path)

    included.sort(key=lambda p: normalize_rel(p.relative_to(ROOT)))
    excluded.sort()
    return included, excluded


def assert_no_secrets(included: list[Path]) -> None:
    forbidden_names = {".blb-secret", "freemius.php"}
    forbidden_suffixes = (".blb-secret",)
    bad: list[str] = []
    for path in included:
        rel = normalize_rel(path.relative_to(ROOT))
        name = path.name
        if name in forbidden_names or any(name.endswith(s) for s in forbidden_suffixes):
            # Allow freemius.config.example.php
            if name == "freemius.config.example.php":
                continue
            if name == "freemius.php" or rel.endswith("/freemius.php") or rel == "config/freemius.php":
                bad.append(rel)
            elif name.startswith(".blb-secret") or "blb-secret" in name:
                bad.append(rel)
    if bad:
        raise SystemExit(
            "Refusing to pack secrets:\n  - " + "\n  - ".join(bad)
        )


def default_zip_path(version: str) -> Path:
    safe = re.sub(r"[^\w.\-]+", "-", version)
    return DIST_DIR / f"{PLUGIN_SLUG}-{safe}.zip"


def write_zip(
    included: list[Path],
    zip_path: Path,
    freemius_config: Path | None,
) -> int:
    zip_path.parent.mkdir(parents=True, exist_ok=True)
    if zip_path.exists():
        zip_path.unlink()

    count = 0
    with zipfile.ZipFile(zip_path, "w", compression=zipfile.ZIP_DEFLATED) as zf:
        for path in included:
            rel = normalize_rel(path.relative_to(ROOT))
            arcname = f"{PLUGIN_SLUG}/{rel}"
            zf.write(path, arcname)
            count += 1

        if freemius_config is not None:
            if not freemius_config.is_file():
                raise SystemExit(f"Freemius config not found: {freemius_config}")
            zf.write(freemius_config, f"{PLUGIN_SLUG}/config/freemius.php")
            count += 1

    return count


def main() -> int:
    parser = argparse.ArgumentParser(description="Pack commercial plugin ZIP using .distignore")
    parser.add_argument("--dry-run", action="store_true", help="List files that would be packed")
    parser.add_argument("--list-excluded", action="store_true", help="List excluded relative paths")
    parser.add_argument("--out", type=Path, default=None, help="Output ZIP path")
    parser.add_argument(
        "--freemius-config",
        type=Path,
        default=None,
        help="Optional production config/freemius.php to inject into the ZIP",
    )
    args = parser.parse_args()

    version = plugin_version()
    patterns = load_distignore_patterns()
    rules = compile_rules(patterns)
    included, excluded = iter_pack_files(rules)
    assert_no_secrets(included)

    if args.list_excluded:
        for rel in excluded:
            print(rel)
        print(f"\n{len(excluded)} excluded, {len(included)} would be packed (v{version})", file=sys.stderr)
        return 0

    if args.dry_run:
        for path in included:
            print(normalize_rel(path.relative_to(ROOT)))
        if args.freemius_config:
            print("config/freemius.php  # injected from --freemius-config")
        print(f"\n{len(included)} files would be packed (v{version})", file=sys.stderr)
        if any(normalize_rel(p.relative_to(ROOT)).startswith("scripts/") for p in included):
            print("ERROR: scripts/ leaked into pack", file=sys.stderr)
            return 1
        return 0

    zip_path = args.out.resolve() if args.out else default_zip_path(version)
    count = write_zip(included, zip_path, args.freemius_config)

    # Verify archive contents never include secrets / scripts / marketing.
    with zipfile.ZipFile(zip_path, "r") as zf:
        names = zf.namelist()

    leaked: list[str] = []
    for n in names:
        if "/scripts/" in n or n.endswith("/scripts"):
            leaked.append(n)
        elif "translation-cache.json" in n:
            leaked.append(n)
        elif "/marketing/" in n:
            leaked.append(n)
        elif "/.blb-secret" in n or n.endswith(".blb-secret"):
            leaked.append(n)
        elif n.endswith("/config/freemius.php") and args.freemius_config is None:
            leaked.append(n)

    if leaked:
        zip_path.unlink(missing_ok=True)
        raise SystemExit("Pack verification failed; leaked paths:\n  - " + "\n  - ".join(leaked[:20]))

    size_mb = zip_path.stat().st_size / (1024 * 1024)
    print(f"Wrote {zip_path}")
    print(f"Version: {version}")
    print(f"Files:   {count}")
    print(f"Size:    {size_mb:.2f} MB")
    print(f"Excluded:{len(excluded)} paths via .distignore + hard excludes")
    if args.freemius_config:
        print(f"Injected config/freemius.php from {args.freemius_config}")
    else:
        print("Note: config/freemius.php not included (use --freemius-config for production keys).")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
