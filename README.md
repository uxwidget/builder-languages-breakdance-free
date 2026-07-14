# Builder Languages for Breakdance — Free

Companion WordPress plugin that loads language packs for the **Breakdance Builder** UI, Breakdance admin/PHP screens, and first-party elements.

**Requires** Breakdance Builder installed and active. Without Breakdance, the plugin shows an admin notice and does not load catalogues.

By [UX Widget](https://uxwidget.com/).

## Free languages

This edition includes **2 product languages** (+ native American English):

- `pt_BR` — Portuguese (Brazil)
- `es_ES` — Spanish (Spain)
- `en_US` — American English (native Breakdance; not a catalogue from this plugin)

Download latest ZIP: **[Releases →](https://github.com/uxwidget/builder-languages-breakdance-free/releases/latest)**

## Upgrade to Pro

Need more languages or agency multi-site licenses?

**[Get Pro →](https://uxwidget.com/builder-languages-breakdance/)**

Pro unlocks **17 languages** (including `pt_PT`, `it_IT`, `fr_FR`, `de_DE`, `es_LA`, `nl_NL`, `pl_PL`, `ru_RU`, `ar`, `he_IL`, `hi_IN`, `ja_JP`, `ko_KR`, `zh_CN`, `en_GB`, plus Free locales) and Freemius license tiers (1–50 sites).

## Install

1. Download `builder-languages-breakdance.zip` from [Releases](https://github.com/uxwidget/builder-languages-breakdance-free/releases/latest).
2. WordPress → Plugins → Add New → Upload Plugin.
3. Activate **Builder Languages for Breakdance**.
4. Open **Breakdance → Languages** and choose Portuguese (Brazil) or Spanish (Spain).
5. Reload the Breakdance Builder (`Ctrl+Shift+R`) after changing language.

## How it works (short)

- Builder JS strings: filter `breakdance_i18n_json` + `languages/breakdance-{locale}.json` / `breakdance-elements-{locale}.json`
- Breakdance admin/PHP: gettext `breakdance` domain via `.mo`
- Elements: `breakdance-elements` domain
- Locale aliases: `translation-fallbacks.json`

Settings and diagnostics: **Breakdance → Languages**.

## Docs in this repo

- `docs/USER-GUIDE.md`
- `docs/FAQ.md`
- `docs/TROUBLESHOOTING.md`
- `marketing/FREE-EDITION.md` — Free vs Pro distribution notes

## License

GPLv2 or later — see `LICENSE.md` / `readme.txt`.
