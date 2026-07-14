# Builder Languages for Breakdance — Free edition

Site: `language-free` · Path: `wp-content/plugins/builder-languages-breakdance/`  
Repo: https://github.com/uxwidget/builder-languages-breakdance-free

## What this build is

| | Free | Pro (`blb01`) |
|--|------|----------------|
| Version | `0.2.12` | SemVer próprio (upload Freemius Paid) |
| Flag | `BREAKDANCE_LANGUAGES_IS_FREE` | absent / false |
| Languages on disk | `pt_BR`, `es_ES` (+ `en_US` native reset) | 17 |
| Locale registry | `config/pt-es.json` | `config/supported-locales.json` |
| Distribution | **GitHub Releases** | **Freemius Paid only** |
| Freemius SDK in ZIP | yes (upsell / checkout only) | yes (license + updates) |

## Distribution model (current)

```
LP "Start free"  →  GitHub Free release ZIP
LP "Buy …"       →  Freemius checkout  →  Pro ZIP + license + updates
```

- **Do not** rely on Freemius Deployments column Free for public Free downloads.
- Freemius Deployments: upload **Pro ZIP only** (Paid column). Treat product as Pro distribution channel.
- Free keeps Freemius SDK so in-plugin **Upgrade today!** / Guarante Versão Pro still open checkout.

## GitHub Free release checklist

1. `python scripts/pack-release.py --freemius-config config/freemius.php`
2. Nome do asset: `builder-languages-breakdance.zip` (pasta do plugin; sem sufixo `-FREE-`)
3. `gh release create v{version} ./builder-languages-breakdance.zip --title "…" --notes "…"`
4. LP link: `https://github.com/uxwidget/builder-languages-breakdance-free/releases/latest`

## Freemius (Pro only — you in dashboard)

1. Deployments → upload **Pro** ZIP into **Paid** only.
2. Do not use Freemius Free column as the public Free download for the LP.
3. Mark Pro tag **Released** for paid updates.
4. Checkouts: `https://checkout.freemius.com/plugin/30587/plan/56028/licenses/{N}/`

## In-plugin upsell

- Admin bar + banner + license tip on Free settings.
- Always visible for admins — Free → Pro conversion path via Freemius checkout.
