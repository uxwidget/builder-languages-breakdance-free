#!/usr/bin/env python3
"""Read/write Builder Languages (.update.blb) private release manifest.

Format (text, one line):
  BLB1.<base64url(zlib(json))>.<hmac_sha256_hex>

The HMAC uses BLB_MANIFEST_SECRET from the environment (or --secret).
Without the secret, anyone can still *decode* the payload (version is not a secret),
but cannot forge a valid signature for your updater.

Usage:
  set BLB_MANIFEST_SECRET=your-long-secret
  python scripts/blb-manifest.py write
  python scripts/blb-manifest.py read
  python scripts/blb-manifest.py verify
"""

from __future__ import annotations

import argparse
import base64
import hashlib
import hmac
import json
import os
import re
import sys
import time
import zlib
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
MANIFEST_PATH = ROOT / ".update.blb"
SECRET_PATH = ROOT / ".blb-secret"
PLUGIN_FILE = ROOT / "builder-languages-breakdance.php"
MAGIC = "BLB1"
PRODUCT = "builder-languages-breakdance"


def b64url_encode(data: bytes) -> str:
    return base64.urlsafe_b64encode(data).decode("ascii").rstrip("=")


def b64url_decode(text: str) -> bytes:
    padding = "=" * (-len(text) % 4)
    return base64.urlsafe_b64decode(text + padding)


def plugin_version() -> str:
    source = PLUGIN_FILE.read_text(encoding="utf-8")
    match = re.search(r"^\s*\*\s*Version:\s*(.+)$", source, re.MULTILINE)
    if not match:
        raise SystemExit(f"Version header not found in {PLUGIN_FILE.name}")
    return match.group(1).strip()


def locale_count() -> int:
    registry = ROOT / "config" / "pt-es.json"
    if not registry.is_file():
        return 0
    data = json.loads(registry.read_text(encoding="utf-8"))
    locales = data.get("locales") or {}
    return len([code for code, meta in locales.items() if (meta or {}).get("status") != "baseline"])


def resolve_secret(cli_secret: str | None) -> str:
    if cli_secret:
        return cli_secret

    env_secret = os.environ.get("BLB_MANIFEST_SECRET", "")
    if env_secret:
        return env_secret

    if SECRET_PATH.is_file():
        local = SECRET_PATH.read_text(encoding="utf-8").strip()
        if local:
            return local

    raise SystemExit(
        "Missing secret. Use one of:\n"
        "  1) env BLB_MANIFEST_SECRET\n"
        "  2) local file .blb-secret (gitignored)\n"
        "  3) --secret\n"
        "Never commit the secret. Keep the GitHub repository private."
    )


def ensure_local_secret() -> str:
    """Create .blb-secret once if missing; return the secret value."""
    if SECRET_PATH.is_file():
        existing = SECRET_PATH.read_text(encoding="utf-8").strip()
        if existing:
            return existing

    import secrets

    value = secrets.token_urlsafe(48)
    SECRET_PATH.write_text(value + "\n", encoding="utf-8")
    try:
        os.chmod(SECRET_PATH, 0o600)
    except OSError:
        pass
    print(f"created {SECRET_PATH.name} (gitignored — do not commit)")
    return value


def build_payload(channel: str, notes: str) -> dict:
    return {
        "product": PRODUCT,
        "slug": PRODUCT,
        "version": plugin_version(),
        "channel": channel,
        "locales": locale_count(),
        "released_at": time.strftime("%Y-%m-%dT%H:%M:%SZ", time.gmtime()),
        "notes": notes,
        "requires": {
            "wordpress": "6.0",
            "php": "7.4",
            "breakdance": "*",
        },
    }


def encode_manifest(payload: dict, secret: str) -> str:
    raw = json.dumps(payload, ensure_ascii=False, separators=(",", ":")).encode("utf-8")
    body = b64url_encode(zlib.compress(raw, level=9))
    signature = hmac.new(secret.encode("utf-8"), f"{MAGIC}.{body}".encode("utf-8"), hashlib.sha256).hexdigest()
    return f"{MAGIC}.{body}.{signature}\n"


def decode_manifest(text: str) -> tuple[dict, str]:
    line = text.strip()
    parts = line.split(".")
    if len(parts) != 3 or parts[0] != MAGIC:
        raise ValueError("Invalid .update.blb format")
    magic, body, signature = parts
    raw = zlib.decompress(b64url_decode(body))
    payload = json.loads(raw.decode("utf-8"))
    return payload, signature


def verify_manifest(text: str, secret: str) -> dict:
    line = text.strip()
    parts = line.split(".")
    if len(parts) != 3 or parts[0] != MAGIC:
        raise ValueError("Invalid .update.blb format")
    magic, body, signature = parts
    expected = hmac.new(secret.encode("utf-8"), f"{magic}.{body}".encode("utf-8"), hashlib.sha256).hexdigest()
    if not hmac.compare_digest(expected, signature):
        raise ValueError("Invalid .update.blb signature")
    payload, _ = decode_manifest(text)
    return payload


def cmd_write(args: argparse.Namespace) -> int:
    if args.secret:
        secret = args.secret
    elif os.environ.get("BLB_MANIFEST_SECRET"):
        secret = os.environ["BLB_MANIFEST_SECRET"]
    else:
        secret = ensure_local_secret()

    payload = build_payload(args.channel, args.notes)
    MANIFEST_PATH.write_text(encode_manifest(payload, secret), encoding="ascii")
    print(f"wrote {MANIFEST_PATH.name}")
    print(json.dumps(payload, ensure_ascii=False, indent=2))
    return 0


def cmd_read(args: argparse.Namespace) -> int:
    if not MANIFEST_PATH.is_file():
        raise SystemExit(f"missing {MANIFEST_PATH}")
    payload, signature = decode_manifest(MANIFEST_PATH.read_text(encoding="ascii"))
    print(json.dumps(payload, ensure_ascii=False, indent=2))
    print(f"signature: {signature[:16]}…")
    return 0


def cmd_verify(args: argparse.Namespace) -> int:
    secret = resolve_secret(args.secret)
    if not MANIFEST_PATH.is_file():
        raise SystemExit(f"missing {MANIFEST_PATH}")
    payload = verify_manifest(MANIFEST_PATH.read_text(encoding="ascii"), secret)
    print("OK — signature valid")
    print(json.dumps(payload, ensure_ascii=False, indent=2))
    return 0


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--secret", default=None, help="HMAC secret (prefer BLB_MANIFEST_SECRET env)")
    sub = parser.add_subparsers(dest="command", required=True)

    write = sub.add_parser("write", help="Generate .update.blb from plugin Version header")
    write.add_argument("--channel", default="stable", choices=("stable", "beta", "dev"))
    write.add_argument("--notes", default="")
    write.set_defaults(func=cmd_write)

    read = sub.add_parser("read", help="Decode .update.blb (no signature check)")
    read.set_defaults(func=cmd_read)

    verify = sub.add_parser("verify", help="Decode and verify HMAC signature")
    verify.set_defaults(func=cmd_verify)

    args = parser.parse_args()
    return args.func(args)


if __name__ == "__main__":
    raise SystemExit(main())
