<?php
/**
 * Plugin Name: Builder Languages for Breakdance
 * Plugin URI: https://uxwidget.com/builder-languages-breakdance
 * Description: ★★★★★ Translate the Breakdance Builder interface instantly into Spanish (es_ES) and Portuguese (pt_BR). Give your local clients a native dashboard. Upgrade to PRO for 17 languages and multi-site agencies.
 * Author: UX Widget
 * Author URI: https://uxwidget.com
 * Version: 0.2.12
 * Requires Plugins: breakdance
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: breakdance-languages
 * Domain Path: /languages
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
define('BREAKDANCE_LANGUAGES_VERSION', '0.2.12');
/** Free build: only pt_BR + es_ES catalogues ship (anti-piracy hard split). */
define('BREAKDANCE_LANGUAGES_IS_FREE', true);
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
        BREAKDANCE_LANGUAGES_PATH . 'includes/site-channel.php',
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
        BREAKDANCE_LANGUAGES_PATH . 'includes/free-upsell.php',
    ];
    foreach ($files as $file) {
        if (
            $file === BREAKDANCE_LANGUAGES_PATH . 'includes/free-upsell.php'
            && (!defined('BREAKDANCE_LANGUAGES_IS_FREE') || !BREAKDANCE_LANGUAGES_IS_FREE)
        ) {
            continue;
        }
        if (is_readable($file)) {
            require_once $file;
        }
    }
}
breakdance_languages_load_files();

register_deactivation_hook(
    BREAKDANCE_LANGUAGES_FILE,
    static function (): void {
        if (function_exists('breakdance_languages_freemius_clear_license_cache')) {
            breakdance_languages_freemius_clear_license_cache();
        }
    }
);

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

    // Free edition: permanent Upgrade link in Plugins list (not tied to 90-day banner dismiss).
    if (defined('BREAKDANCE_LANGUAGES_IS_FREE') && BREAKDANCE_LANGUAGES_IS_FREE) {
        $checkout = function_exists('breakdance_languages_checkout_url')
            ? breakdance_languages_checkout_url()
            : 'https://uxwidget.com/builder-languages-breakdance/#pricing';

        $pro_label = 'Upgrade to Pro';
        if (function_exists('breakdance_languages_get_settings_ui_strings')) {
            $ui = breakdance_languages_get_settings_ui_strings(
                function_exists('breakdance_languages_get_user_builder_locale')
                    ? breakdance_languages_get_user_builder_locale()
                    : 'en_US'
            );
            if (!empty($ui['license_upgrade'])) {
                $pro_label = (string) $ui['license_upgrade'];
            }
        }

        $links[] = sprintf(
            '<a href="%s" style="color:#1a5f4a;font-weight:600;">%s</a>',
            esc_url($checkout),
            esc_html($pro_label)
        );
    }

    return $links;
});

/**
 * Fallback Plugins-list description (short). Full layout is injected by mu-plugin JS.
 */
function breakdance_languages_free_plugin_list_description(): string
{
    return '★★★★★ Translate the Breakdance Builder interface instantly into Spanish (es_ES) and Portuguese (pt_BR). '
        . 'Give your local clients a native dashboard. Upgrade to PRO for 17 languages and multi-site agencies.';
}

/**
 * Keep HTML description when the plugin is active (header alone can be kses-stripped).
 */
add_filter('all_plugins', static function (array $plugins): array {
    if (!defined('BREAKDANCE_LANGUAGES_IS_FREE') || !BREAKDANCE_LANGUAGES_IS_FREE) {
        return $plugins;
    }

    $basename = plugin_basename(BREAKDANCE_LANGUAGES_FILE);

    if (isset($plugins[$basename])) {
        $plugins[$basename]['Description'] = breakdance_languages_free_plugin_list_description();
    }

    return $plugins;
});

/**
 * Allow span[style] + anchors in plugin descriptions on the Plugins screen.
 *
 * @param array<string, array<string, bool>> $allowed
 * @return array<string, array<string, bool>>
 */
add_filter('wp_kses_allowed_html', static function (array $allowed, $context): array {
    if (!is_admin()) {
        return $allowed;
    }

    if ($context !== 'post' && $context !== 'data') {
        return $allowed;
    }

    if (!defined('BREAKDANCE_LANGUAGES_IS_FREE') || !BREAKDANCE_LANGUAGES_IS_FREE) {
        return $allowed;
    }

    if (!isset($allowed['span']) || !is_array($allowed['span'])) {
        $allowed['span'] = [];
    }

    $allowed['span']['style'] = true;
    $allowed['span']['class'] = true;
    $allowed['span']['aria-label'] = true;

    if (!isset($allowed['a']) || !is_array($allowed['a'])) {
        $allowed['a'] = [];
    }

    $allowed['a']['href'] = true;
    $allowed['a']['title'] = true;
    $allowed['a']['target'] = true;
    $allowed['a']['rel'] = true;

    if (!isset($allowed['br'])) {
        $allowed['br'] = [];
    }

    if (!isset($allowed['strong'])) {
        $allowed['strong'] = [];
    }

    return $allowed;
}, 10, 2);

/**
 * Extra meta under the plugin row (Free): keep Pro hook (stars live in Description).
 */
add_filter('plugin_row_meta', static function (array $links, string $file): array {
    if ($file !== plugin_basename(BREAKDANCE_LANGUAGES_FILE)) {
        return $links;
    }

    if (!defined('BREAKDANCE_LANGUAGES_IS_FREE') || !BREAKDANCE_LANGUAGES_IS_FREE) {
        return $links;
    }

    $url = 'https://uxwidget.com/builder-languages-breakdance/#pricing';

    $links[] = sprintf(
        '<a href="%s"><strong>%s</strong></a>',
        esc_url($url),
        esc_html__('Upgrade Now — 17 languages', 'breakdance-languages')
    );

    return $links;
}, 10, 2);

/**
 * Style yellow stars on the Plugins screen (Free).
 */
add_action('admin_enqueue_scripts', static function (string $hook): void {
    if ($hook !== 'plugins.php') {
        return;
    }

    if (!defined('BREAKDANCE_LANGUAGES_IS_FREE') || !BREAKDANCE_LANGUAGES_IS_FREE) {
        return;
    }

    $css = BREAKDANCE_LANGUAGES_PATH . 'admin/assets/free-upsell.css';

    if (!is_readable($css)) {
        return;
    }

    wp_enqueue_style(
        'breakdance-languages-free-upsell',
        plugins_url('admin/assets/free-upsell.css', BREAKDANCE_LANGUAGES_FILE),
        [],
        BREAKDANCE_LANGUAGES_VERSION
    );

    // Yellow stars — inline so color does not depend on a separate stylesheet race.
    $inline = '.plugins .plugin-description .blb-desc-stars{color:#dba617!important;font-size:18px!important;letter-spacing:2px}';
    wp_add_inline_style('breakdance-languages-free-upsell', $inline);
});

/**
 * Print yellow-star CSS even if wp_enqueue order fails (Plugins screen only).
 */
add_action('admin_head-plugins.php', static function (): void {
    if (!defined('BREAKDANCE_LANGUAGES_IS_FREE') || !BREAKDANCE_LANGUAGES_IS_FREE) {
        return;
    }
    echo '<style id="blb-free-plugin-stars">.plugins .plugin-description .blb-desc-stars{color:#dba617!important;font-size:18px!important;letter-spacing:2px}</style>' . "\n";
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
