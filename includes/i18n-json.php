<?php
/**
 * Build merged Breakdance Builder i18n JSON payloads.
 *
 * @package Breakdance_Languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Merge custom language JSON into Breakdance's builder payload.
 *
 * @param string      $json   Base JSON from Breakdance.
 * @param string|null $locale Locale code to load.
 * @return string
 */
function breakdance_languages_build_i18n_json(string $json, ?string $locale = null): string
{
    static $cache = [];

    if (!breakdance_languages_is_breakdance_active()) {
        return $json !== '' ? $json : '{}';
    }

    $locale = $locale ?: breakdance_languages_resolve_locale();

    if ($locale === null) {
        return $json !== '' ? $json : '{}';
    }

    $cache_key = $locale . ':' . md5($json);

    if (isset($cache[$cache_key])) {
        return $cache[$cache_key];
    }

    $base = json_decode($json !== '' ? $json : '{}', true);

    if (!is_array($base)) {
        $base = [];
    }

    foreach (['breakdance', 'breakdance-elements'] as $domain) {
        $path = BREAKDANCE_LANGUAGES_PATH . 'languages/' . $domain . '-' . $locale . '.json';

        if (!is_readable($path)) {
            continue;
        }

        $custom = json_decode((string) file_get_contents($path), true);

        if (!is_array($custom)) {
            continue;
        }

        $base = breakdance_languages_merge_jed_json($base, $custom);

        if ($domain === 'breakdance-elements') {
            $base = breakdance_languages_merge_jed_domain_into_domain(
                $base,
                $custom,
                'breakdance-elements',
                'breakdance'
            );
        }
    }

    $cache[$cache_key] = (string) wp_json_encode($base);

    return $cache[$cache_key];
}

/**
 * Reload Breakdance PHP textdomains for the resolved locale.
 */
function breakdance_languages_reload_php_textdomains(?string $locale = null): void
{
    breakdance_languages_reload_all_textdomains($locale);
}
