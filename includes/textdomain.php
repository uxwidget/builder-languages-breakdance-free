<?php
/**
 * Builder Languages for Breakdance — Textdomain.
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

const BREAKDANCE_LANGUAGES_TEXTDOMAINS = ['breakdance', 'breakdance-elements', 'breakdance-languages'];

add_filter('plugin_locale', 'breakdance_languages_filter_plugin_locale', 10, 2);
add_filter('load_textdomain_mofile', 'breakdance_languages_filter_mofile', 10, 2);

add_action('init', static function (): void {
    if (breakdance_languages_is_breakdance_ajax_request()) {
        return;
    }

    if (!breakdance_languages_can_apply_translations()) {
        return;
    }

    breakdance_languages_reload_all_textdomains();
}, 2);

/**
 * Use the resolved builder locale when loading plugin translations.
 *
 * @param string $locale
 * @param string $domain
 * @return string
 */
function breakdance_languages_filter_plugin_locale(string $locale, string $domain): string
{
    if (!in_array($domain, BREAKDANCE_LANGUAGES_TEXTDOMAINS, true)) {
        return $locale;
    }

    if (!breakdance_languages_can_apply_translations() || !breakdance_languages_should_apply_builder_locale()) {
        return $locale;
    }

    $resolved = breakdance_languages_resolve_locale($locale);

    return $resolved ?: $locale;
}

/**
 * Redirect Breakdance translation loads to this plugin's language packs.
 *
 * @param string $mofile
 * @param string $domain
 * @return string
 */
function breakdance_languages_filter_mofile(string $mofile, string $domain): string
{
    if (!in_array($domain, BREAKDANCE_LANGUAGES_TEXTDOMAINS, true)) {
        return $mofile;
    }

    if (!breakdance_languages_can_apply_translations() || !breakdance_languages_should_apply_builder_locale()) {
        return $mofile;
    }

    $locale = breakdance_languages_resolve_locale();

    if ($locale === null) {
        return $mofile;
    }

    $custom = breakdance_languages_get_mofile_path($domain, $locale);

    return $custom ?? $mofile;
}

/**
 * Load this plugin's admin UI textdomain for the resolved locale.
 */
function breakdance_languages_load_plugin_textdomain(?string $locale = null): void
{
    $locale = $locale ?: breakdance_languages_resolve_locale();

    if ($locale === null) {
        $locale = determine_locale();
    }

    $mofile = breakdance_languages_get_mofile_path('breakdance-languages', $locale);

    if ($mofile !== null) {
        unload_textdomain('breakdance-languages');
        load_textdomain('breakdance-languages', $mofile);
        return;
    }

    load_plugin_textdomain(
        'breakdance-languages',
        false,
        dirname(plugin_basename(BREAKDANCE_LANGUAGES_FILE)) . '/languages'
    );
}

/**
 * Reload plugin and Breakdance textdomains for the resolved locale.
 */
function breakdance_languages_reload_all_textdomains(?string $locale = null): void
{
    breakdance_languages_load_plugin_textdomain($locale);
    breakdance_languages_load_breakdance_textdomains($locale);
}

/**
 * Load Breakdance PHP textdomains for the resolved locale.
 */
function breakdance_languages_load_breakdance_textdomains(?string $locale = null): void
{
    if (!breakdance_languages_can_apply_translations() || !breakdance_languages_is_breakdance_active()) {
        return;
    }

    $locale = $locale ?: breakdance_languages_resolve_locale();

    if ($locale === null) {
        return;
    }

    foreach (['breakdance', 'breakdance-elements'] as $domain) {
        $mofile = breakdance_languages_get_mofile_path($domain, $locale);

        if ($mofile === null) {
            continue;
        }

        unload_textdomain($domain);
        load_textdomain($domain, $mofile);
    }
}

/**
 * Resolve the MO file path for a domain and locale.
 *
 * Compiles PO files on demand when MO files are missing.
 */
function breakdance_languages_get_mofile_path(string $domain, string $locale): ?string
{
    $languages_dir = BREAKDANCE_LANGUAGES_PATH . 'languages/';
    $mofile = $languages_dir . $domain . '-' . $locale . '.mo';

    if (is_readable($mofile)) {
        return $mofile;
    }

    $cache_mofile = WP_CONTENT_DIR . '/cache/breakdance-languages/' . $domain . '-' . $locale . '.mo';

    if (is_readable($cache_mofile)) {
        return $cache_mofile;
    }

    $pofile = $languages_dir . $domain . '-' . $locale . '.po';

    if (!is_readable($pofile)) {
        return null;
    }

    $target_mofile = breakdance_languages_get_writable_mofile_path($domain, $locale);

    if (!breakdance_languages_compile_po_to_mo($pofile, $target_mofile)) {
        return null;
    }

    return is_readable($target_mofile) ? $target_mofile : null;
}

/**
 * Pick a writable MO destination, falling back to wp-content/cache when needed.
 */
function breakdance_languages_get_writable_mofile_path(string $domain, string $locale): string
{
    $plugin_mofile = BREAKDANCE_LANGUAGES_PATH . 'languages/' . $domain . '-' . $locale . '.mo';
    $plugin_dir = dirname($plugin_mofile);

    if (is_dir($plugin_dir) && is_writable($plugin_dir)) {
        return $plugin_mofile;
    }

    $cache_dir = WP_CONTENT_DIR . '/cache/breakdance-languages';

    if (!is_dir($cache_dir)) {
        wp_mkdir_p($cache_dir);
    }

    return $cache_dir . '/' . $domain . '-' . $locale . '.mo';
}

/**
 * Compile a PO file into an MO file using WordPress POMO classes.
 */
function breakdance_languages_compile_po_to_mo(string $po_file, string $mo_file): bool
{
    if (!is_readable($po_file)) {
        return false;
    }

    require_once ABSPATH . WPINC . '/pomo/po.php';
    require_once ABSPATH . WPINC . '/pomo/mo.php';

    $po = new PO();

    if (!$po->import_from_file($po_file)) {
        return false;
    }

    $mo = new MO();

    foreach ($po->entries as $entry) {
        $mo->add_entry($entry);
    }

    foreach ($po->headers as $header => $value) {
        $mo->set_header($header, $value);
    }

    return $mo->export_to_file($mo_file);
}
