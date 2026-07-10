# Breakdance Languages - Changelog

## ux-0.1.0

Initial release.

- Added Breakdance admin/PHP translations.
- Added Breakdance Builder JavaScript translations.
- Added Breakdance Elements translations.
- Included 8 translated language packs: Portuguese Brazil, Portuguese, French, German, Spanish, Arabic, Japanese, and English International, with American English (`en_US`) as the default baseline and runtime fallback.
- Added compatibility layer for Breakdance Builder translation JSON.
- Added Breakdance dependency guard and admin notice.
- Added WordPress plugin dependency header for Breakdance.
- Added Breakdance Elements JSON merge fallback for Builder translations.
- Changed gettext loading to priority 20 so custom language packs override Breakdance defaults.
- Added release QA script for placeholder spacing checks.
- Added distribution ignore list for build-only files.
- Added `en_US` language files and locale fallback support.
- Added diagnostics page under `Settings > Breakdance Languages`.
- Added safe Freemius bootstrap scaffold for commercial builds.
- Fixed Portuguese placeholder spacing issues reported by release QA.
