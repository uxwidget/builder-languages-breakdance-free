#!/usr/bin/env python3
"""Shared locale registry for Breakdance Languages scripts."""

from __future__ import annotations

import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
REGISTRY_PATH = ROOT / "config" / "supported-locales.json"


def load_registry() -> dict:
    if not REGISTRY_PATH.is_file():
        return {}
    return json.loads(REGISTRY_PATH.read_text(encoding="utf-8"))


def locale_codes(include_baseline: bool = True) -> list[str]:
    registry = load_registry()
    codes = list((registry.get("locales") or {}).keys())
    if include_baseline:
        return codes
    return [
        code
        for code in codes
        if (registry.get("locales") or {}).get(code, {}).get("status") != "baseline"
    ]


def translatable_locale_codes() -> list[str]:
    registry = load_registry()
    codes: list[str] = []
    for code, meta in (registry.get("locales") or {}).items():
        if meta.get("status") == "baseline":
            continue
        codes.append(code)
    return codes


def release_gate_locales() -> list[str]:
    registry = load_registry()
    return list(registry.get("release_gate_locales") or ("pt_BR", "pt_PT", "it_IT"))


def locale_meta(locale: str) -> dict:
    registry = load_registry()
    return dict((registry.get("locales") or {}).get(locale, {}))


def plugin_meta(locale: str) -> tuple[str, str, str]:
    meta = locale_meta(locale)
    return (
        meta.get("language_team", locale),
        locale,
        meta.get("plural_forms", "nplurals=2; plural=(n != 1);"),
    )


def translate_target(locale: str) -> str | None:
    target = locale_meta(locale).get("translate_target")
    return target if isinstance(target, str) and target else None
