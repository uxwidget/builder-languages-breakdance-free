# Breakdance Languages - Installation

## Requirements

- WordPress 6.0 or newer
- PHP 7.4 or newer
- Breakdance Builder installed and active
- A WordPress user locale supported by Breakdance Languages

## Included Languages

- Portuguese Brazil (`pt_BR`)
- Portuguese (`pt_PT`)
- French (`fr_FR`)
- German (`de_DE`)
- Spanish (`es_ES`)
- Arabic (`ar`)
- English International (`en_GB`)
- English United States (`en_US`)

## Install From ZIP

1. Download the plugin ZIP file from your UX Widget account or purchase email.
2. In WordPress, go to `Plugins > Add New Plugin`.
3. Click `Upload Plugin`.
4. Select the `breakdance-languages.zip` file.
5. Click `Install Now`.
6. Activate `Breakdance Languages`.

## Set Your WordPress Language

Breakdance Languages follows the current WordPress user locale.

For the whole site:

1. Go to `Settings > General`.
2. Change `Site Language`.
3. Save changes.

For your user only:

1. Go to `Users > Profile`.
2. Change `Language`.
3. Save changes.

## Clear Cache

After activation or update:

1. Clear any WordPress cache plugin.
2. Clear server/CDN cache if enabled.
3. Open Breakdance Builder in a new browser tab.
4. Hard refresh the browser:
   - Windows/Linux: `Ctrl + F5`
   - macOS: `Cmd + Shift + R`

## Verify Installation

Open a page with Breakdance Builder. Interface labels, controls, elements, and admin texts should load in your selected language.

You can also verify the active language pack in `Settings > Breakdance Languages`.

If the interface is still in English, check:

- The selected WordPress user language.
- Whether the language is included in Breakdance Languages.
- Whether Breakdance Builder is active.
- Whether browser/server cache is serving old JavaScript.
