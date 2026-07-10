# Breakdance Languages - Compatibility

## Supported Product

Breakdance Languages is built for Breakdance Builder and first-party Breakdance Elements.

Breakdance Builder must be active. The plugin declares `Requires Plugins: breakdance` and also performs a runtime dependency check before loading language files.

## Supported WordPress

- WordPress 6.0 or newer
- PHP 7.4 or newer

## Supported Locales

- `pt_BR`
- `pt_PT`
- `fr_FR`
- `de_DE`
- `es_ES`
- `ar`
- `ja_JP`
- `it_IT`
- `en_GB`
- `en_US`

## Known Boundaries

Breakdance may include strings in multiple places:

- PHP gettext files
- JavaScript builder translation payloads
- Element catalogues
- Compiled JavaScript bundles
- Third-party package code

Breakdance Languages covers the available translation catalogues and selected builder JSON payloads. Hardcoded strings can require separate compatibility patches.

For Breakdance Builder JavaScript, the plugin merges both `breakdance-{locale}.json` and `breakdance-elements-{locale}.json`. Because Breakdance 2.8.0 registers the filtered JSON on the fixed `breakdance` domain, element translations are mirrored into that domain as a compatibility fallback.

Locale fallback mappings are loaded from `translation-fallbacks.json`.

## WooCommerce

Breakdance WooCommerce strings often use the WooCommerce text domain. Those translations are normally provided by WooCommerce language packs.

## Cache And Optimization Plugins

JavaScript optimization plugins can delay or cache Builder assets. If translations do not appear after updating, exclude Breakdance Builder pages from JS optimization and clear cache.
