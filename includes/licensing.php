<?php
/**
 * Builder Languages for Breakdance — Licensing.
 *
 * @package Builder Languages Breakdance
 * @author  UX Widget
 * @link    https://uxwidget.com
 * @license GPL-2.0-or-later
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Whether Freemius credentials and SDK are present.
 */
function breakdance_languages_freemius_is_configured(): bool
{
    if (
        !defined('BREAKDANCE_LANGUAGES_FREEMIUS_ID') ||
        !defined('BREAKDANCE_LANGUAGES_FREEMIUS_PUBLIC_KEY')
    ) {
        return false;
    }

    if (breakdance_languages_freemius_should_skip_sdk_load()) {
        return true;
    }

    return breakdance_languages_fs() !== null;
}

/**
 * Whether the current request targets a local development site.
 */
function breakdance_languages_is_local_dev_site(): bool
{
    return breakdance_languages_freemius_is_local_host();
}

/**
 * wp-config constant name for the Freemius developer secret key.
 */
function breakdance_languages_freemius_secret_key_constant(): string
{
    return 'WP_FS__breakdance-languages_SECRET_KEY';
}

/**
 * Read the Freemius developer secret key from wp-config.php.
 */
function breakdance_languages_get_freemius_secret_key(): ?string
{
    $constant = breakdance_languages_freemius_secret_key_constant();

    if (!defined($constant)) {
        return null;
    }

    $value = constant($constant);

    if (!is_string($value) || $value === '') {
        return null;
    }

    return $value;
}

/**
 * Whether Freemius dev bypass is active (local testing with secret key).
 */
function breakdance_languages_is_freemius_dev_bypass_active(): bool
{
    if (!defined('WP_FS__DEV_MODE') || !WP_FS__DEV_MODE) {
        return false;
    }

    if (!breakdance_languages_is_local_dev_site()) {
        return false;
    }

    return breakdance_languages_get_freemius_secret_key() !== null;
}

/**
 * Whether Freemius reports an active paid/trial license.
 */
function breakdance_languages_freemius_has_active_license(): bool
{
    if (breakdance_languages_is_freemius_dev_bypass_active()) {
        return true;
    }

    $freemius = breakdance_languages_fs();

    if ($freemius === null) {
        if (breakdance_languages_freemius_should_skip_sdk_load()) {
            $cached = breakdance_languages_freemius_cache_license_status();

            if ($cached !== null) {
                return $cached;
            }

            $inferred = breakdance_languages_freemius_infer_license_active_from_accounts();

            if ($inferred !== null) {
                breakdance_languages_freemius_cache_license_status($inferred);

                return $inferred;
            }
        }

        if (
            !defined('BREAKDANCE_LANGUAGES_FREEMIUS_ID') ||
            !defined('BREAKDANCE_LANGUAGES_FREEMIUS_PUBLIC_KEY')
        ) {
            if (defined('BREAKDANCE_LANGUAGES_RELEASE_BUILD') && BREAKDANCE_LANGUAGES_RELEASE_BUILD) {
                return false;
            }

            return breakdance_languages_is_local_dev_site();
        }

        return false;
    }

    if (method_exists($freemius, 'can_use_premium_code')) {
        return (bool) $freemius->can_use_premium_code();
    }

    return (bool) $freemius->is_paying();
}

/**
 * Whether the current install has an active license.
 *
 * Dev builds without Freemius config are treated as licensed.
 * Local dev with WP_FS__DEV_MODE + secret key also unlocks premium features.
 */
function breakdance_languages_is_licensed(): bool
{
    if (breakdance_languages_is_freemius_dev_bypass_active()) {
        return true;
    }

    return breakdance_languages_freemius_has_active_license();
}

/**
 * Whether translations may load for the current request.
 *
 * The builder intentionally skips the Freemius SDK, so rely on the cached
 * license flag and fs_accounts inference before treating the install as unlicensed.
 */
function breakdance_languages_can_apply_translations(): bool
{
    if (breakdance_languages_is_licensed()) {
        return true;
    }

    if (
        !breakdance_languages_is_builder_runtime_request()
        && !breakdance_languages_should_apply_builder_locale()
    ) {
        return false;
    }

    $cached = breakdance_languages_freemius_cache_license_status();

    if ($cached === false) {
        return false;
    }

    if ($cached === true) {
        return true;
    }

    $inferred = breakdance_languages_freemius_infer_license_active_from_accounts();

    if ($inferred !== null) {
        breakdance_languages_freemius_cache_license_status($inferred);

        return $inferred;
    }

    $site = breakdance_languages_freemius_get_site_install_data();

    if ($site === null) {
        return false;
    }

    if (breakdance_languages_freemius_site_is_trial($site)) {
        breakdance_languages_freemius_cache_license_status(true);

        return true;
    }

    $license_id = $site['license_id'] ?? null;

    if (!is_numeric($license_id) || (int) $license_id <= 0) {
        return false;
    }

    $license = breakdance_languages_freemius_find_license_record((int) $license_id);

    if ($license === null) {
        return false;
    }

    $active = breakdance_languages_freemius_license_record_can_use_premium($license);
    breakdance_languages_freemius_cache_license_status($active);

    return $active;
}

/**
 * @deprecated Use breakdance_languages_is_licensed().
 */
function breakdance_languages_is_pro(): bool
{
    return breakdance_languages_is_licensed();
}

/**
 * Locale codes available for the active request.
 *
 * @return list<string>
 */
function breakdance_languages_runtime_locale_codes(): array
{
    if (!breakdance_languages_can_apply_translations()) {
        return [];
    }

    return breakdance_languages_supported_locale_codes();
}

/**
 * Locale codes available for licensed installs.
 *
 * @return list<string>
 */
function breakdance_languages_plan_locale_codes(): array
{
    if (!breakdance_languages_is_licensed()) {
        return [];
    }

    return breakdance_languages_supported_locale_codes();
}

/**
 * Locales available for licensed installs.
 *
 * @return array<string, string>
 */
function breakdance_languages_plan_locales(): array
{
    $all = breakdance_languages_supported_locales();
    $allowed = array_flip(breakdance_languages_plan_locale_codes());

    return array_intersect_key($all, $allowed);
}

/**
 * Purchase URL for new customers.
 */
function breakdance_languages_purchase_url(): string
{
    return 'https://uxwidget.com/builder-languages-breakdance';
}

/**
 * Account URL for licensed customers.
 */
function breakdance_languages_account_url(): string
{
    return breakdance_languages_license_manage_url();
}

/**
 * Admin URL for the Freemius license activation or account screen.
 *
 * Opt-in lives on page=breakdance-languages; after connect, after_* filters
 * return the user to Breakdance → Languages (status panel).
 */
function breakdance_languages_license_manage_url(): string
{
    if (!breakdance_languages_freemius_is_configured()) {
        return breakdance_languages_settings_page_url();
    }

    $freemius = breakdance_languages_fs();

    if ($freemius === null) {
        return admin_url('admin.php?page=breakdance-languages');
    }

    if (
        method_exists($freemius, 'is_registered') &&
        $freemius->is_registered() &&
        method_exists($freemius, 'get_account_url')
    ) {
        return (string) $freemius->get_account_url();
    }

    return admin_url('admin.php?page=breakdance-languages');
}

/**
 * Pricing / checkout URL.
 */
function breakdance_languages_checkout_url(): string
{
    $freemius = breakdance_languages_fs();

    if ($freemius !== null && method_exists($freemius, 'get_upgrade_url')) {
        return (string) $freemius->get_upgrade_url();
    }

    return breakdance_languages_purchase_url();
}

/**
 * Human-readable license status label.
 */
function breakdance_languages_license_status_label(): string
{
    if (!breakdance_languages_freemius_is_configured()) {
        return __('Development mode', 'breakdance-languages');
    }

    if (breakdance_languages_is_freemius_dev_bypass_active()) {
        return __('Development mode', 'breakdance-languages');
    }

    if (breakdance_languages_freemius_has_active_license()) {
        return __('Active', 'breakdance-languages');
    }

    return __('Inactive', 'breakdance-languages');
}

/**
 * Whether the current install is using a Freemius sandbox/dev environment.
 */
function breakdance_languages_is_freemius_sandbox_environment(): bool
{
    if (breakdance_languages_is_freemius_dev_bypass_active()) {
        return true;
    }

    if (defined('WP_FS__DEV_MODE') && WP_FS__DEV_MODE) {
        return true;
    }

    $freemius = breakdance_languages_fs();

    if ($freemius !== null && method_exists($freemius, 'is_payments_sandbox')) {
        return (bool) $freemius->is_payments_sandbox();
    }

    return false;
}

/**
 * License panel state for the Languages settings screen.
 *
 * @return array{
 *     status: string,
 *     show_buy: bool,
 *     account_url: string,
 *     checkout_url: string,
 *     environment_key: ?string,
 *     show_environment: bool
 * }
 */
function breakdance_languages_get_license_panel_context(): array
{
    if (!breakdance_languages_freemius_is_configured()) {
        $status = 'dev';
    } elseif (breakdance_languages_is_freemius_dev_bypass_active()) {
        $status = 'dev';
    } elseif (!breakdance_languages_is_licensed()) {
        $status = 'inactive';
    } else {
        $status = 'active';
    }

    $environment_key = null;

    if (breakdance_languages_is_freemius_dev_bypass_active()) {
        $environment_key = 'license_environment_dev';
    } elseif (breakdance_languages_is_freemius_sandbox_environment()) {
        $environment_key = 'license_environment_sandbox';
    } elseif ($status === 'active') {
        $environment_key = 'license_environment_production';
    }

    return [
        'status' => $status,
        'show_buy' => $status === 'inactive' && breakdance_languages_freemius_is_configured(),
        'account_url' => breakdance_languages_license_manage_url(),
        'checkout_url' => breakdance_languages_checkout_url(),
        'environment_key' => $environment_key,
        'show_environment' => $environment_key !== null,
    ];
}

/**
 * Hide Freemius activation nags while local dev bypass is active.
 *
 * Premium-only plugins cannot use Freemius anonymous mode, so the SDK still
 * shows "Complete activation" notices even when our bypass unlocks translations.
 */
function breakdance_languages_register_freemius_dev_bypass_filters(): void
{
    if (!breakdance_languages_is_freemius_dev_bypass_active()) {
        return;
    }

    $freemius = breakdance_languages_fs();

    if ($freemius === null) {
        return;
    }

    $freemius->add_filter(
        'show_admin_notice',
        static function ($show, $msg) {
            if (!breakdance_languages_is_freemius_dev_bypass_active()) {
                return $show;
            }

            $fs = breakdance_languages_fs();

            if ($fs === null || $fs->is_registered()) {
                return $show;
            }

            $manager_id = isset($msg['manager_id']) ? (string) $msg['manager_id'] : '';

            if ($manager_id === 'breakdance-languages') {
                return false;
            }

            return $show;
        },
        10,
        2
    );
}

breakdance_languages_register_freemius_dev_bypass_filters();
