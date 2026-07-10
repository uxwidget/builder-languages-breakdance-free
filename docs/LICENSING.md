# Breakdance Languages - Licensing Notes

## Model

Breakdance Languages is a **premium-only** product. There is no permanent free tier.

- Customers purchase a license (Personal / Business / Agency).
- Translations load only when a valid license is active.
- Dev builds without Freemius credentials run in development mode with all features enabled.

## Freemius Integration

Follow the official guide: [Integrating your WordPress product with the Freemius SDK](https://freemius.com/help/documentation/wordpress/integration-with-sdk/)

### Setup steps

1. Create the product in the Freemius Developer Dashboard.
2. Add plans and pricing (1 / 5 / unlimited sites).
3. Download the WordPress SDK and place it at `vendor/freemius/start.php`.
4. Copy `config/freemius.config.example.php` to `config/freemius.php`.
5. Paste your product ID and public key from the SDK Integration page.
6. Enable development mode in `wp-config.php` while testing:

```php
define( 'WP_FS__DEV_MODE', true );
```

7. Deactivate and reactivate the plugin, then activate a test license.
8. Deploy the product through Freemius when ready.

### License check in code

The plugin uses `breakdance_languages_is_licensed()`, which maps to Freemius `can_use_premium_code()`.

## Suggested Plans

- Personal: 1 site.
- Business: 5 sites.
- Agency: unlimited client sites.

See `marketing/PRICING.md` for draft pricing.

## Breakdance Trademark Notice

Breakdance is a trademark of its respective owner. Breakdance Languages is an independent product by UX Widget and is not affiliated with or endorsed by Breakdance.
