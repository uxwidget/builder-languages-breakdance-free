# Builder Languages for Breakdance — Free edition

Site: `language-free` · Path: `wp-content/plugins/builder-languages-breakdance/`

## What this build is

| | Free | Pro (`blb01`) |
|--|------|----------------|
| Version | `0.2.0` | `0.1.14+` |
| Flag | `BREAKDANCE_LANGUAGES_IS_FREE` | absent / false |
| Languages on disk | `pt_BR`, `es_ES` (+ `en_US` native reset) | 17 |
| Locale registry | `config/pt-es.json` | `config/supported-locales.json` |
| Freemius | `is_premium_only` = false | currently premium-only |

## Freemius dashboard (you)

1. Product → Freemium (not Premium-only).
2. Add Free plan.
3. Upload this Free ZIP as the free version; keep Pro ZIP as premium.
4. Same slug: `breakdance-languages` (upgrade replaces folder).

## In-plugin upsell

- Admin bar (top-secondary) + banner on Languages / Dashboard / Plugins / Breakdance screens.
- Always visible for admins (no dismiss) — Free → Pro is the conversion path.

## LP (fase 1)

- Button **Download Free** → Freemius Free download URL.
- Pro stays on Breakdance Marketplace.

## WordPress.org (fase 2)

Same Free ZIP, WordPress.org Compliant = Yes.
