# Builder Languages for Breakdance

Complementary WordPress plugin that loads language packs for the Breakdance Builder interface, Breakdance admin/PHP screens, and first-party Breakdance Elements.

Breakdance Builder must be installed and active. If Builder Languages for Breakdance is active without Breakdance, the plugin shows an admin notice and does not attempt to load language files.

## Included Locales

Eight translated language packs plus American English as the default baseline:

- `pt_BR` - Portuguese (Brazil)
- `pt_PT` - Portuguese
- `fr_FR` - French
- `de_DE` - German
- `es_ES` - Spanish
- `ar` - Arabic
- `ja_JP` - Japanese
- `en_GB` - English (International)
- `en_US` - English (United States) — default American English baseline and runtime fallback

## How It Works

Breakdance prints Builder translations through `wp.i18n` and exposes the `breakdance_i18n_json` filter. This plugin hooks that filter and merges matching `languages/breakdance-{locale}.json` and `languages/breakdance-elements-{locale}.json` files into the Builder translation payload.

Breakdance 2.8.0 registers the filtered Builder JSON on the fixed `breakdance` JavaScript domain. For compatibility, Breakdance Languages also mirrors `breakdance-elements` JSON translations into the `breakdance` domain, while preserving the original `breakdance-elements` domain data.

Breakdance admin/PHP translations are loaded as standard gettext files using `languages/breakdance-{locale}.po` and `languages/breakdance-{locale}.mo` on the original `breakdance` text domain. The plugin loads these files on `plugins_loaded` priority 20 so they can override Breakdance's default language loading.

First-party element translations are loaded on the `breakdance-elements` text domain using `languages/breakdance-elements-{locale}.po` and `languages/breakdance-elements-{locale}.mo`.

Locale fallback rules are stored in `translation-fallbacks.json`. When no exact locale match is found, the plugin falls back to `en_US` (American English).

## WordPress Profile Language vs Builder Language

Breakdance Languages uses **two language layers**:

| Layer | Where to set it | What it controls |
| --- | --- | --- |
| WordPress profile language | `Users > Profile > Language` | Base locale for WordPress and parts of Breakdance |
| Builder language | `Breakdance > Languages` | Preferred language for the Breakdance Builder |

### Explicit builder language

When you choose a **specific language** in `Breakdance > Languages` (not Auto), the plugin also updates the **WordPress user profile locale** for the current user. This keeps Breakdance, WordPress, and the builder on the same language without a separate trip to `Users > Profile > Language`.

### Auto mode

If you choose **Use WordPress profile language**, the builder follows the profile locale. Change the profile language to switch the builder when using Auto.

### After changing language

Reload the Breakdance Builder manually after saving (`Ctrl+Shift+R` or close and reopen the builder tab). An open builder tab is not reloaded automatically.

The Languages settings screen shows your current WordPress profile language and whether it matches the builder choice.

See also: `docs/USER-GUIDE.md`, `docs/FAQ.md`, and `docs/TROUBLESHOOTING.md`.

Diagnostics are available in WordPress under `Settings > Breakdance Languages`.

Freemius licensing is scaffolded for commercial builds. It activates only when Freemius product constants and the SDK are present.

## Translation Status

The plugin includes complete editable catalogues generated from Breakdance's `.pot` files:

- Admin/PHP: `breakdance/languages/breakdance.pot` -> `breakdance-{locale}.po/.mo`
- Builder JS: `breakdance/languages/breakdance-builder.pot` -> `breakdance-builder-{locale}.po` -> `breakdance-{locale}.json`
- Elements PHP/runtime: `breakdance/subplugins/breakdance-elements/languages/breakdance-elements.pot` -> `breakdance-elements-{locale}.po/.mo`
- Element controls: `breakdance/subplugins/breakdance-elements/languages/breakdance-elements-builder.pot` -> `breakdance-elements-{locale}.po/.mo` and `breakdance-elements-{locale}.json`

WooCommerce strings inside `subplugins/breakdance-woocommerce` mostly use the `woocommerce` text domain, so they are translated by WooCommerce language packs, not by this plugin.

Some strings in compiled JavaScript or vendor code can still be hardcoded and absent from the `.pot` files. Those require either a targeted runtime replacement layer or an AI-assisted translation workflow.

## Documentation

- **Dev setup (clean Local site):** `docs/DEV-SETUP.md`
- Installation: `docs/INSTALLATION.md`
- User guide: `docs/USER-GUIDE.md`
- FAQ: `docs/FAQ.md`
- Troubleshooting: `docs/TROUBLESHOOTING.md`
- Compatibility: `docs/COMPATIBILITY.md`
- Changelog: `docs/CHANGELOG.md`
- Support policy: `docs/SUPPORT-POLICY.md`
- Refund policy draft: `docs/REFUND-POLICY.md`
- Licensing notes: `docs/LICENSING.md`

## Sales Materials

- Sales page draft: `marketing/SALES-PAGE.md`
- Pricing draft: `marketing/PRICING.md`
- Launch checklist: `marketing/LAUNCH-CHECKLIST.md`

## Release QA

- **All-in-one:** `python scripts/validate-all.py`
- Catalogue structure: `python scripts/verify-catalogues.py`
- Placeholder spacing: `python scripts/qa-placeholders.py --summary --all-supported`
- MT spacing repair: `python scripts/fix-placeholder-spacing.py` (then `--json-only` + `compile-mo.py`)
- Distribution exclusions: `.distignore`

**Release gate:** `pt_BR`, `pt_PT`, and `it_IT` must show **0** suspicious placeholders. Other locales are tracked but may retain MT residuals (especially `de_DE`).
