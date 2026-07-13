<?php
/**
 * Builder Languages for Breakdance — Freemius bootstrap.
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

$config = BREAKDANCE_LANGUAGES_PATH . 'config/freemius.php';

if (is_readable($config)) {
    require_once $config;
}

$release_build = BREAKDANCE_LANGUAGES_PATH . 'config/release-build.php';

if (is_readable($release_build)) {
    require_once $release_build;
}

/**
 * Whether the current HTTP host looks like a local development site.
 *
 * Only clearly-local environments qualify. Public TLDs like `.dev` and
 * production-like suffixes such as `.staging` are NOT treated as local.
 */
function breakdance_languages_freemius_is_local_host(): bool
{
    if (defined('WP_FS__IS_LOCALHOST') && WP_FS__IS_LOCALHOST) {
        return true;
    }

    if (
        function_exists('wp_get_environment_type')
        && wp_get_environment_type() === 'local'
    ) {
        return true;
    }

    $host = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';

    if ($host === '') {
        return false;
    }

    $host_only = preg_replace('/:\d+$/', '', $host) ?? $host;

    if (
        $host_only === 'localhost'
        || $host_only === '127.0.0.1'
        || $host_only === '::1'
        || strpos($host_only, 'localhost.') === 0
    ) {
        return true;
    }

    foreach (['.local', '.test'] as $suffix) {
        $suffix_length = strlen($suffix);

        if (strlen($host_only) >= $suffix_length && substr($host_only, -$suffix_length) === $suffix) {
            return true;
        }
    }

    return false;
}

/**
 * Whether wp-config has the local Freemius dev/sandbox environment enabled.
 */
function breakdance_languages_freemius_local_dev_env(): bool
{
    if (!defined('WP_FS__DEV_MODE') || !WP_FS__DEV_MODE) {
        return false;
    }

    $secret_key = function_exists('breakdance_languages_get_freemius_secret_key')
        ? breakdance_languages_get_freemius_secret_key()
        : null;

    if ($secret_key === null) {
        return false;
    }

    return breakdance_languages_freemius_is_local_host();
}

/**
 * Whether the current request is part of the Breakdance builder runtime.
 */
function breakdance_languages_freemius_is_builder_request(): bool
{
    if (!breakdance_languages_is_breakdance_active()) {
        return false;
    }

    if (isset($_POST['breakdance_ajax_at_any_url'])) {
        return true;
    }

    if (defined('DOING_AJAX') && DOING_AJAX) {
        $action = isset($_REQUEST['action']) ? (string) wp_unslash($_REQUEST['action']) : '';

        if ($action !== '' && strpos($action, 'breakdance') === 0) {
            return true;
        }
    }

    if (is_admin()) {
        return false;
    }

    $query_key = (defined('BREAKDANCE_MODE') && BREAKDANCE_MODE === 'oxygen') ? 'oxygen' : 'breakdance';

    if (!isset($_GET[$query_key])) {
        return false;
    }

    $mode = sanitize_text_field(wp_unslash((string) $_GET[$query_key]));

    return in_array($mode, [
        'builder',
        'templates',
        'design_library',
        'regenerate-cache',
        'onboarding-app',
    ], true);
}

/**
 * Whether Freemius AJAX handlers should still bootstrap the SDK.
 */
function breakdance_languages_freemius_is_sdk_ajax_request(): bool
{
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        return false;
    }

    $action = isset($_REQUEST['action']) ? (string) wp_unslash($_REQUEST['action']) : '';

    if ($action === '') {
        return false;
    }

    if (strpos($action, 'breakdance_languages_') === 0) {
        return true;
    }

    return strpos($action, 'fs_') === 0;
}

/**
 * Skip Freemius outside wp-admin license screens.
 *
 * Loading the SDK on builder pages and Breakdance AJAX freezes the elements panel.
 */
function breakdance_languages_freemius_should_skip_sdk_load(): bool
{
    if (breakdance_languages_freemius_is_builder_request()) {
        return true;
    }

    if (!is_admin()) {
        return true;
    }

    if (defined('DOING_AJAX') && DOING_AJAX) {
        return !breakdance_languages_freemius_is_sdk_ajax_request();
    }

    return false;
}

/**
 * Read the Freemius accounts blob without booting the SDK.
 *
 * @return array<string, mixed>
 */
function breakdance_languages_freemius_get_accounts(): array
{
    $accounts = get_option('fs_accounts', []);

    return is_array($accounts) ? $accounts : [];
}

/**
 * Whether a stored Freemius license record can unlock premium features.
 *
 * @param array<string, mixed>|object $license
 */
function breakdance_languages_freemius_license_record_can_use_premium($license): bool
{
    if (!is_array($license) && !is_object($license)) {
        return false;
    }

    $data = (array) $license;
    $id = $data['id'] ?? null;

    if (!is_numeric($id) || (int) $id <= 0) {
        return false;
    }

    if (!empty($data['is_cancelled'])) {
        return false;
    }

    $expiration = $data['expiration'] ?? null;

    if ($expiration !== null && $expiration !== '') {
        $expires_at = strtotime((string) $expiration);

        if ($expires_at !== false && $expires_at < time() && !empty($data['is_block_features'])) {
            return false;
        }
    }

    return true;
}

/**
 * Whether a stored Freemius site install is in an active trial.
 *
 * @param array<string, mixed>|object $site
 */
function breakdance_languages_freemius_site_is_trial($site): bool
{
    if (!is_array($site) && !is_object($site)) {
        return false;
    }

    $data = (array) $site;
    $trial_plan_id = $data['trial_plan_id'] ?? null;
    $trial_ends = $data['trial_ends'] ?? null;

    if (!is_numeric($trial_plan_id) || $trial_ends === null || $trial_ends === '') {
        return false;
    }

    $ends_at = strtotime((string) $trial_ends);

    return $ends_at !== false && $ends_at > time();
}

/**
 * Read the stored Freemius install record for this plugin.
 *
 * @return array<string, mixed>|null
 */
function breakdance_languages_freemius_get_site_install_data(): ?array
{
    $accounts = breakdance_languages_freemius_get_accounts();
    $sites = $accounts['sites'] ?? null;

    if (!is_array($sites)) {
        return null;
    }

    $site = $sites['breakdance-languages'] ?? null;

    if ($site === null) {
        return null;
    }

    return (array) $site;
}

/**
 * Find a stored Freemius license record by ID.
 *
 * @return array<string, mixed>|null
 */
function breakdance_languages_freemius_find_license_record(int $license_id): ?array
{
    if ($license_id <= 0) {
        return null;
    }

    $accounts = breakdance_languages_freemius_get_accounts();
    $all_licenses = $accounts['all_licenses'] ?? null;

    if (!is_array($all_licenses)) {
        return null;
    }

    $plugin_id = defined('BREAKDANCE_LANGUAGES_FREEMIUS_ID')
        ? (int) BREAKDANCE_LANGUAGES_FREEMIUS_ID
        : 0;

    $candidate_lists = [];

    if ($plugin_id > 0) {
        if (isset($all_licenses[$plugin_id]) && is_array($all_licenses[$plugin_id])) {
            $candidate_lists[] = $all_licenses[$plugin_id];
        }

        if (isset($all_licenses[(string) $plugin_id]) && is_array($all_licenses[(string) $plugin_id])) {
            $candidate_lists[] = $all_licenses[(string) $plugin_id];
        }
    }

    $candidate_lists[] = $all_licenses;

    foreach ($candidate_lists as $plugin_licenses) {
        if (!is_array($plugin_licenses)) {
            continue;
        }

        foreach ($plugin_licenses as $license) {
            if (!is_array($license) && !is_object($license)) {
                continue;
            }

            $license_data = (array) $license;
            $id = $license_data['id'] ?? null;

            if ((int) $id === $license_id) {
                return $license_data;
            }
        }
    }

    return null;
}

/**
 * Infer license status from fs_accounts when the SDK is skipped (builder/AJAX).
 */
function breakdance_languages_freemius_infer_license_active_from_accounts(): ?bool
{
    if (!defined('BREAKDANCE_LANGUAGES_FREEMIUS_ID')) {
        return null;
    }

    $site_data = breakdance_languages_freemius_get_site_install_data();

    if ($site_data === null) {
        return breakdance_languages_freemius_get_accounts() === [] ? null : false;
    }

    if (breakdance_languages_freemius_site_is_trial($site_data)) {
        return true;
    }

    $license_id = $site_data['license_id'] ?? null;

    if (!is_numeric($license_id) || (int) $license_id <= 0) {
        return false;
    }

    $license = breakdance_languages_freemius_find_license_record((int) $license_id);

    if ($license === null) {
        // Conservative: license_id alone is not enough without a local license record.
        return false;
    }

    return breakdance_languages_freemius_license_record_can_use_premium($license);
}

/**
 * Persist license status for requests where the SDK is intentionally skipped.
 *
 * Cached values expire after a TTL so revoked licenses stop unlocking the
 * builder without requiring an immediate admin visit.
 */
function breakdance_languages_freemius_cache_license_status(?bool $active = null): ?bool
{
    $option = 'breakdance_languages_fs_license_active';
    $meta = 'breakdance_languages_fs_license_active_at';
    $ttl = defined('HOUR_IN_SECONDS') ? 6 * HOUR_IN_SECONDS : 21600;

    if ($active !== null) {
        update_option($option, $active ? '1' : '0', false);
        update_option($meta, (string) time(), false);

        return $active;
    }

    $cached = get_option($option, null);
    $cached_at = (int) get_option($meta, 0);

    if (is_string($cached) && $cached_at > 0 && (time() - $cached_at) < $ttl) {
        return $cached === '1';
    }

    $inferred = breakdance_languages_freemius_infer_license_active_from_accounts();

    if ($inferred !== null) {
        update_option($option, $inferred ? '1' : '0', false);
        update_option($meta, (string) time(), false);

        return $inferred;
    }

    return null;
}

/**
 * Drop the cached Freemius license flag (revocation / uninstall / deactivation).
 */
function breakdance_languages_freemius_clear_license_cache(): void
{
    delete_option('breakdance_languages_fs_license_active');
    delete_option('breakdance_languages_fs_license_active_at');
}

/**
 * Silence Freemius SDK trace logs on local dev (prevents builder freezes).
 */
function breakdance_languages_freemius_prepare_sdk_debug_flags(): void
{
    if (defined('WP_FS__DEBUG_SDK') || defined('WP_FS__ECHO_DEBUG_SDK')) {
        return;
    }

    if (!breakdance_languages_freemius_local_dev_env()) {
        return;
    }

    define('WP_FS__DEBUG_SDK', false);
    define('WP_FS__ECHO_DEBUG_SDK', false);
}

if (!function_exists('breakdance_languages_fs')) {
    /**
     * @return \Freemius|null
     */
    function breakdance_languages_fs()
    {
        global $breakdance_languages_fs;

        if (!isset($breakdance_languages_fs)) {
            if (breakdance_languages_freemius_should_skip_sdk_load()) {
                $breakdance_languages_fs = null;

                return null;
            }

            if (
                !defined('BREAKDANCE_LANGUAGES_FREEMIUS_ID') ||
                !defined('BREAKDANCE_LANGUAGES_FREEMIUS_PUBLIC_KEY')
            ) {
                $breakdance_languages_fs = null;

                return null;
            }

            $sdk = BREAKDANCE_LANGUAGES_PATH . 'vendor/freemius/start.php';

            if (!is_readable($sdk)) {
                $breakdance_languages_fs = null;

                return null;
            }

            breakdance_languages_freemius_prepare_sdk_debug_flags();

            require_once $sdk;

            // Local + WP_FS__DEV_MODE + product secret → Freemius Sandbox API.
            // Live licenses ("Create License" in live dashboard) need is_live true and DEV_MODE off.
            $use_sandbox_api = breakdance_languages_freemius_local_dev_env();

            $breakdance_languages_fs = fs_dynamic_init([
                'id' => (string) BREAKDANCE_LANGUAGES_FREEMIUS_ID,
                'slug' => 'breakdance-languages',
                'type' => 'plugin',
                'public_key' => (string) BREAKDANCE_LANGUAGES_FREEMIUS_PUBLIC_KEY,
                'is_premium_only' => true,
                'has_premium_version' => true,
                'has_paid_plans' => true,
                'menu' => [
                    'slug' => 'breakdance-languages',
                    'parent' => [
                        'slug' => 'breakdance',
                    ],
                    'first-path' => 'admin.php?page=breakdance-languages',
                    'account' => true,
                    'contact' => false,
                    'support' => false,
                ],
                'is_live' => !$use_sandbox_api,
            ]);

            breakdance_languages_fs_register_redirect_filters($breakdance_languages_fs);

            if ($breakdance_languages_fs !== null && method_exists($breakdance_languages_fs, 'add_action')) {
                $breakdance_languages_fs->add_action(
                    'after_uninstall',
                    'breakdance_languages_freemius_clear_license_cache'
                );
                $breakdance_languages_fs->add_action(
                    'after_account_connection',
                    static function () use ($breakdance_languages_fs): void {
                        if (method_exists($breakdance_languages_fs, 'can_use_premium_code')) {
                            breakdance_languages_freemius_cache_license_status(
                                (bool) $breakdance_languages_fs->can_use_premium_code()
                            );
                        }
                    }
                );
                $breakdance_languages_fs->add_action(
                    'after_license_activation',
                    static function () use ($breakdance_languages_fs): void {
                        if (method_exists($breakdance_languages_fs, 'can_use_premium_code')) {
                            breakdance_languages_freemius_cache_license_status(
                                (bool) $breakdance_languages_fs->can_use_premium_code()
                            );
                        } else {
                            breakdance_languages_freemius_cache_license_status(true);
                        }
                    }
                );
                $breakdance_languages_fs->add_action(
                    'after_license_deactivation',
                    static function (): void {
                        breakdance_languages_freemius_cache_license_status(false);
                    }
                );
            }

            if (
                $breakdance_languages_fs !== null &&
                method_exists($breakdance_languages_fs, 'can_use_premium_code')
            ) {
                breakdance_languages_freemius_cache_license_status(
                    (bool) $breakdance_languages_fs->can_use_premium_code()
                );
            }
        }

        return $breakdance_languages_fs;
    }

    /**
     * URL for Breakdance → Languages after Freemius flows.
     */
    function breakdance_languages_fs_settings_url(): string
    {
        return breakdance_languages_settings_page_url();
    }

    /**
     * Product display name for Freemius UI (not the parent Breakdance builder).
     */
    function breakdance_languages_fs_plugin_title(): string
    {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $data = get_plugin_data(BREAKDANCE_LANGUAGES_FILE, false, false);
        $name = isset($data['Name']) ? trim((string) $data['Name']) : '';

        return $name !== '' ? $name : 'Builder Languages for Breakdance';
    }

    /**
     * Premium connect copy must use our plugin name, not Freemius/Breakdance title.
     *
     * @param string $message
     * @param string $user_first_name
     * @param string $plugin_name
     */
    function breakdance_languages_fs_connect_message_premium($message, $user_first_name = '', $plugin_name = ''): string
    {
        $title = esc_html(breakdance_languages_fs_plugin_title());
        $line1 = sprintf(
            /* translators: %s: product name */
            __('Welcome to %s!', 'breakdance-languages'),
            $title
        );
        $line2 = esc_html__('To get started, please enter your license key:', 'breakdance-languages');

        // Keep inline/phrasing tags — Freemius wraps this string in a <p>.
        return '<span class="bdl-fs-connect-message" style="display:block;text-align:center;line-height:1.5;margin-bottom:1.25em;">'
            . '<strong style="display:block;">' . $line1 . '</strong>'
            . '<span style="display:block;margin-top:0.35em;">' . $line2 . '</span>'
            . '</span>';
    }

    /**
     * @param \Freemius|null $freemius
     */
    function breakdance_languages_fs_register_redirect_filters($freemius): void
    {
        if ($freemius === null || !is_object($freemius)) {
            return;
        }

        // Keep Freemius opt-in on page=breakdance-languages (do not override connect_url).
        // After opt-in / skip, land on Breakdance → Languages with license status.
        $freemius->add_filter('after_skip_url', 'breakdance_languages_fs_settings_url');
        $freemius->add_filter('after_connect_url', 'breakdance_languages_fs_settings_url');
        $freemius->add_filter('after_pending_connect_url', 'breakdance_languages_fs_settings_url');
        $freemius->add_filter('plugin_title', 'breakdance_languages_fs_plugin_title');
        $freemius->add_filter('connect-message_on-premium', 'breakdance_languages_fs_connect_message_premium', 10, 3);
        $freemius->add_action('connect/after', 'breakdance_languages_fs_connect_styles');
        // Premium-only: skip Freemius deactivation survey (Anonymous feedback / Skip & Deactivate).
        $freemius->add_filter('show_deactivation_feedback_form', '__return_false');
        // Freemius license keys can exceed 32 chars / contain special characters.
        $freemius->add_filter('license_key_maxlength', static function (): int {
            return 64;
        });
    }

    /**
     * Center Freemius connect helpers (license resend link).
     */
    function breakdance_languages_fs_connect_styles(): void
    {
        echo '<style id="bdl-fs-connect-styles">'
            . '#fs_connect .fs-license-key-container .show-license-resend-modal{'
            . 'display:block;text-align:center;margin-top:0.75em;'
            . '}'
            . '</style>';
    }

    add_action('admin_init', static function (): void {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        breakdance_languages_fs();
    }, 0);

    /**
     * Temporary credentials diag — only when Freemius is not yet licensed.
     */
    add_action('admin_notices', static function (): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (defined('BREAKDANCE_LANGUAGES_RELEASE_BUILD') && BREAKDANCE_LANGUAGES_RELEASE_BUILD) {
            return;
        }

        $page = isset($_GET['page']) ? (string) wp_unslash($_GET['page']) : '';

        if ($page !== 'breakdance-languages' && strpos($page, 'breakdance-languages') !== 0) {
            return;
        }

        if (function_exists('breakdance_languages_is_licensed') && breakdance_languages_is_licensed()) {
            return;
        }

        $id = defined('BREAKDANCE_LANGUAGES_FREEMIUS_ID')
            ? (string) BREAKDANCE_LANGUAGES_FREEMIUS_ID
            : '(missing config/freemius.php)';
        $pk = defined('BREAKDANCE_LANGUAGES_FREEMIUS_PUBLIC_KEY')
            ? (string) BREAKDANCE_LANGUAGES_FREEMIUS_PUBLIC_KEY
            : '(missing)';

        $sandbox = function_exists('breakdance_languages_freemius_local_dev_env')
            && breakdance_languages_freemius_local_dev_env();
        $mode = $sandbox ? 'SANDBOX' : 'LIVE';

        $fs = function_exists('breakdance_languages_fs') ? breakdance_languages_fs() : null;
        $sdk_live = 'n/a';

        if ($fs !== null && method_exists($fs, 'is_live')) {
            $sdk_live = $fs->is_live() ? 'true' : 'false';
        }

        echo '<div class="notice notice-warning"><p><strong>Freemius diag</strong> — '
            . 'API: <code>' . esc_html($mode) . '</code> · '
            . 'id: <code>' . esc_html($id) . '</code> · '
            . 'public_key: <code>' . esc_html($pk) . '</code> · '
            . 'sdk is_live: <code>' . esc_html($sdk_live) . '</code></p></div>';
    });
}

/**
 * Prime the license cache before builder runtime hooks when the SDK is skipped.
 */
add_action('plugins_loaded', static function (): void {
    if (!breakdance_languages_freemius_should_skip_sdk_load()) {
        return;
    }

    breakdance_languages_freemius_cache_license_status();
}, 5);

/**
 * Refresh the cached license flag whenever the Freemius SDK boots in wp-admin.
 */
add_action('admin_init', static function (): void {
    if (breakdance_languages_freemius_should_skip_sdk_load()) {
        return;
    }

    $freemius = breakdance_languages_fs();

    if ($freemius === null || !method_exists($freemius, 'can_use_premium_code')) {
        return;
    }

    breakdance_languages_freemius_cache_license_status(
        (bool) $freemius->can_use_premium_code()
    );
}, 20);
