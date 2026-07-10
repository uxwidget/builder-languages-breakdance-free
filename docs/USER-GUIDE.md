# Breakdance Languages - User Guide

Breakdance Languages adds language packs for Breakdance Builder, Breakdance admin screens, and first-party Breakdance elements.

## What It Translates

- Breakdance Builder interface labels
- Element names
- Element controls
- Preset sections
- Breakdance admin/PHP strings
- First-party Breakdance Elements strings

## What It Does Not Replace

Some text may still appear in English when it is:

- Hardcoded inside compiled Breakdance JavaScript.
- Loaded from third-party plugins.
- Loaded by WooCommerce or another plugin text domain.
- Added by custom code, templates, or snippets.
- Stored as content inside your WordPress database.

## Language Selection

Go to `Breakdance > Languages` to choose the Breakdance Builder language.

### Explicit builder language (recommended)

Choose a specific locale in `Breakdance > Languages` (for example `Português (Brasil)`). The plugin saves the builder preference **and syncs the WordPress user profile locale** for the current user, so Breakdance and WordPress stay aligned.

Then reload the Breakdance Builder (`Ctrl+Shift+R` or close and reopen the builder tab).

### Auto mode

**Use WordPress profile language** means the builder follows `Users > Profile > Language`. The plugin does not change the profile in Auto mode.

### After changing language

Saving a new language does not automatically reload an open builder session. Reload the builder manually.

The Languages screen shows your WordPress profile language and warns if it still differs from the builder choice (for example before sync completes or on older saves).

### Plans

- **Licensed installs:** choose any supported language independently from the WordPress profile, but aligning both is still recommended.
- **Free / limited plans:** translations follow the WordPress profile language when that locale is included in the plan.

If an exact locale file is unavailable, the plugin can use mappings from `translation-fallbacks.json`.

## Diagnostics

Open `Breakdance > Settings > Languages` to verify:

- Breakdance detection.
- Breakdance version.
- WordPress user locale.
- Resolved language pack.
- Required `.mo` and `.json` files.
- Freemius commercial configuration status.

## Updating Translations

When a new version is installed:

1. Update the plugin.
2. Clear caches.
3. Open Breakdance Builder.
4. Hard refresh the browser.

## Manual Translation Corrections

Translation corrections should be made in the `.po` files inside:

`wp-content/plugins/breakdance-languages/languages`

After editing `.po` files:

1. Recompile the matching `.mo` file.
2. Regenerate JSON files for Builder translations when the source is a builder catalogue.
3. Clear cache before testing.

## File Types

- `.po`: editable translation catalogue.
- `.mo`: compiled gettext file loaded by WordPress/PHP.
- `.json`: JavaScript translation payload loaded by Breakdance Builder.
