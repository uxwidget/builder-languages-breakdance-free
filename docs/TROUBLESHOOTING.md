# Breakdance Languages - Troubleshooting

## The Builder Is Still In English

Check these items:

1. Confirm `Breakdance Languages` is active.
2. Confirm `Breakdance Builder` is active.
3. Confirm **Breakdance > Languages** is set to your target locale (explicit choice syncs the WordPress profile automatically).
4. Reload the Breakdance Builder manually after changing language (hard refresh with `Ctrl+Shift+R`).
5. Clear all cache layers (including WP Rocket or similar page cache).
6. Hard refresh the browser again if needed.

### Profile and builder language mismatch

Choosing an explicit language in **Breakdance > Languages** updates the WordPress profile locale for the current user. If a warning still appears, save the language again or set **Users > Profile > Language** manually, then reload the builder.

## Some Text Is Translated And Some Is Not

This usually means the missing text is outside the current translation catalogues. It may be:

- Hardcoded in Breakdance compiled assets.
- Coming from a third-party plugin.
- Coming from WooCommerce.
- Stored as content in WordPress.
- Added by a custom template or code snippet.

## Builder JSON Does Not Update

Clear browser cache and any optimization plugin that combines or delays JavaScript. Breakdance Builder translations are loaded through JavaScript locale data.

## PHP/Admin Text Does Not Update

Confirm the matching `.mo` file exists for your locale:

`languages/breakdance-{locale}.mo`

Example:

`languages/breakdance-pt_BR.mo`

## Arabic Layout Direction

The plugin provides Arabic translations. It does not redesign Breakdance Builder for right-to-left layouts beyond what WordPress and Breakdance already support.

## Report A Missing Translation

Send:

- The exact English text.
- The current wrong translation, if any.
- The expected translation.
- A screenshot.
- Your Breakdance version.
- Your WordPress locale.

