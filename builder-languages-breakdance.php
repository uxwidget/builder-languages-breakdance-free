<?php
/**
 * Plugin Name: Builder Languages for Breakdance
 * Plugin URI: https://uxwidget.com/builder-languages-breakdance
 * Description: Adds professional language packs for the Breakdance Builder interface, admin screens, and first-party elements.
 * Author: UX Widget
 * Author URI: https://uxwidget.com
 * Version: ux-0.1.5
 * Requires Plugins: breakdance
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: breakdance-languages
 * Domain Path: /languages
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}
define('BREAKDANCE_LANGUAGES_VERSION', 'ux-0.1.5');
define('BREAKDANCE_LANGUAGES_FILE', __FILE__);
define('BREAKDANCE_LANGUAGES_PATH', plugin_dir_path(__FILE__));
/**
 * Whether verbose browser-console diagnostics should load in the builder.
 */
function breakdance_languages_is_debug_enabled(): bool
{
    if (defined('BREAKDANCE_LANGUAGES_DEBUG') && BREAKDANCE_LANGUAGES_DEBUG) {
        return true;
    }
    return defined('WP_DEBUG') && WP_DEBUG && breakdance_languages_is_local_dev_site();
}
require_once BREAKDANCE_LANGUAGES_PATH . 'includes/freemius-init.php';
/**
 * Load plugin files.
 */
function breakdance_languages_load_files(): void
{
    $files = [
        BREAKDANCE_LANGUAGES_PATH . 'includes/licensing.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/blb-manifest.php',
        BREAKDANCE_LANGUAGES_PATH . 'admin/admin-menu.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/freemius-menu.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/locale-registry.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/locale.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/geolocation-locale.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/rtl-support.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/priority-locale-i18n.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/element-categories-i18n.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/wp-language-pack.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/runtime.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/builder-runtime.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/textdomain.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/editor-overrides.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/media-i18n.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/global-settings-i18n.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/form-builder-i18n.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/design-library.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/shape-dividers.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/admin-overrides.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/i18n-json.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/ui-strings.php',
        BREAKDANCE_LANGUAGES_PATH . 'includes/builder-sync.php',
        BREAKDANCE_LANGUAGES_PATH . 'admin/settings-page.php',
    ];
    foreach ($files as $file) {
        if (is_readable($file)) {
            require_once $file;
        }
    }
}
breakdance_languages_load_files();
/**
 * Ensure compiled MO catalogs exist for runtime translation loading.
 */
add_action('plugins_loaded', static function (): void {
    if (!function_exists('breakdance_languages_get_mofile_path')) {
        return;
    }
    foreach (['breakdance', 'breakdance-elements'] as $domain) {
        $mofile = BREAKDANCE_LANGUAGES_PATH . 'languages/' . $domain . '-pt_BR.mo';

        if (!is_readable($mofile)) {
            breakdance_languages_get_mofile_path($domain, 'pt_BR');
        }
    }
}, 6);

/**
 * Determine whether Breakdance Builder is currently active.
 */
function breakdance_languages_is_breakdance_active(): bool
{
    return defined('__BREAKDANCE_VERSION') || defined('__BREAKDANCE_PLUGIN_FILE__');
}
add_action('init', static function (): void {
    if (breakdance_languages_is_breakdance_ajax_request()) {
        breakdance_languages_ensure_builder_runtime_textdomains();
        return;
    }

    breakdance_languages_load_plugin_textdomain();
}, 0);
/**
 * Load PHP translations for Breakdance's PHP/admin strings and first-party elements.
 */
add_action('init', static function (): void {
    if (breakdance_languages_is_breakdance_ajax_request()) {
        return;
    }

    if (!breakdance_languages_can_apply_translations()) {
        return;
    }

    breakdance_languages_load_breakdance_textdomains();
}, 1);
/**
 * Replace or merge Breakdance Builder JS translations.
 */
add_filter('breakdance_i18n_json', static function ($json): string {
    if (!breakdance_languages_can_apply_translations()) {
        return is_string($json) ? $json : '{}';
    }

    return breakdance_languages_build_i18n_json(is_string($json) ? $json : '{}');
}, 20);
add_action('admin_notices', static function (): void {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (!breakdance_languages_freemius_is_configured()) {
        return;
    }

    $freemius = breakdance_languages_fs();

    if ($freemius === null) {
        return;
    }

    if (method_exists($freemius, 'is_registered') && $freemius->is_registered()) {
        return;
    }
    $email = wp_get_current_user()->user_email;
    if (!class_exists('Freemius') || !Freemius::is_valid_email($email)) {
        echo '<div class="notice notice-error"><p>';
        echo esc_html__(
            'Builder Languages for Breakdance: Freemius requires a real email address (not @localhost, @test, etc.) to complete license activation. Update your WordPress profile email, then try again.',
            'breakdance-languages'
        );
        echo ' <a href="' . esc_url(admin_url('profile.php')) . '">';
        echo esc_html__('Edit profile', 'breakdance-languages');
        echo '</a></p></div>';
    }
});
add_action('admin_notices', static function (): void {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (!breakdance_languages_is_breakdance_active()) {
        return;
    }

    if (breakdance_languages_is_licensed() || !breakdance_languages_freemius_is_configured()) {
        return;
    }

    echo '<div class="notice notice-warning"><p>';
    echo esc_html__('Builder Languages for Breakdance: activate your license to load translations.', 'breakdance-languages');
    echo ' <a href="' . esc_url(breakdance_languages_account_url()) . '">';
    echo esc_html__('Manage license', 'breakdance-languages');
    echo '</a></p></div>';
});
add_filter('plugin_action_links_' . plugin_basename(BREAKDANCE_LANGUAGES_FILE), static function (array $links): array {
    if (!breakdance_languages_is_breakdance_active()) {
        return $links;
    }

    $settings_link = sprintf(
        '<a href="%s">%s</a>',
        esc_url(breakdance_languages_settings_page_url()),
        esc_html__('Languages', 'breakdance-languages')
    );

    array_unshift($links, $settings_link);

    return $links;
});
/**
 * Show a dependency notice when the language pack is active without Breakdance.
 */
add_action('admin_notices', static function (): void {
    if (breakdance_languages_is_breakdance_active() || !current_user_can('activate_plugins')) {
        return;
    }

    echo '<div class="notice notice-warning"><p>';
    echo esc_html__('Builder Languages for Breakdance is active, but Breakdance Builder is not active. Activate Breakdance Builder to load the language packs.', 'breakdance-languages');
    echo '</p></div>';
});
/**
 * Merge two Jed-style wp.i18n JSON payloads.
 *
 * @param array<string, mixed> $base
 * @param array<string, mixed> $custom
 * @return array<string, mixed>
 */
function breakdance_languages_merge_jed_json(array $base, array $custom): array
{
    if (!isset($base['locale_data']) || !is_array($base['locale_data'])) {
        $base['locale_data'] = [];
    }

    if (!isset($custom['locale_data']) || !is_array($custom['locale_data'])) {
        return $base;
    }

    foreach ($custom['locale_data'] as $domain => $translations) {
        if (!is_string($domain) || !is_array($translations)) {
            continue;
        }

        if (!isset($base['locale_data'][$domain]) || !is_array($base['locale_data'][$domain])) {
            $base['locale_data'][$domain] = [];
        }

        $base['locale_data'][$domain] = array_replace(
            $base['locale_data'][$domain],
            $translations
        );
    }

    return $base;
}

/**
 * Copy one Jed domain into another as a compatibility fallback.
 *
 * Existing keys in the target domain win. Source keys are mirrored only when
 * the target does not already define them (avoids overwriting shared terms).
 *
 * @param array<string, mixed> $base
 * @param array<string, mixed> $custom
 * @return array<string, mixed>
 */
function breakdance_languages_merge_jed_domain_into_domain(
    array $base,
    array $custom,
    string $sourceDomain,
    string $targetDomain
): array {
    if (
        !isset($custom['locale_data'][$sourceDomain]) ||
        !is_array($custom['locale_data'][$sourceDomain])
    ) {
        return $base;
    }

    if (!isset($base['locale_data']) || !is_array($base['locale_data'])) {
        $base['locale_data'] = [];
    }

    if (!isset($base['locale_data'][$targetDomain]) || !is_array($base['locale_data'][$targetDomain])) {
        $base['locale_data'][$targetDomain] = [];
    }

    // array_replace(source, target): later arrays win on key conflicts.
    $base['locale_data'][$targetDomain] = array_replace(
        $custom['locale_data'][$sourceDomain],
        $base['locale_data'][$targetDomain]
    );

    if (isset($base['locale_data'][$targetDomain]['']) && is_array($base['locale_data'][$targetDomain][''])) {
        $base['locale_data'][$targetDomain]['']['domain'] = $targetDomain;
    }

    return $base;
}
