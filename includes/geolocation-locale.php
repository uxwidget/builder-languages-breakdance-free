<?php
/**
 * Builder Languages for Breakdance — Geolocation locale.
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
 * @return array<string, mixed>
 */
function breakdance_languages_get_geolocation_config(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $path = BREAKDANCE_LANGUAGES_PATH . 'config/freemius-geolocation-locales.json';

    if (!is_readable($path)) {
        $config = [];
        return $config;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    $config = is_array($decoded) ? $decoded : [];

    return $config;
}

/**
 * @return array<string, string>
 */
function breakdance_languages_geolocation_country_map(): array
{
    $countries = breakdance_languages_get_geolocation_config()['countries'] ?? [];

    if (!is_array($countries)) {
        return [];
    }

    $map = [];

    foreach ($countries as $country => $locale) {
        if (is_string($country) && is_string($locale)) {
            $map[strtoupper($country)] = $locale;
        }
    }

    return $map;
}

/**
 * Infer locale from Accept-Language (e.g. hi-IN, he-IL, nl-NL, zh-CN).
 */
function breakdance_languages_locale_from_accept_language(): ?string
{
    $header = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])
        ? (string) wp_unslash($_SERVER['HTTP_ACCEPT_LANGUAGE'])
        : '';

    if ($header === '') {
        return null;
    }

    $aliases = breakdance_languages_get_geolocation_config()['locale_aliases'] ?? [];
    $supported = array_flip(breakdance_languages_supported_locale_codes());

    foreach (explode(',', $header) as $part) {
        $tag = strtolower(trim(strtok($part, ';')));

        if ($tag === '') {
            continue;
        }

        $normalized = str_replace('-', '_', $tag);
        $candidates = [$normalized];

        if (strlen($normalized) === 2) {
            $candidates[] = $normalized . '_' . strtoupper($normalized);
        }

        if (preg_match('/^([a-z]{2})_([a-z]{2})$/i', $normalized, $matches)) {
            $candidates[] = strtolower($matches[1]) . '_' . strtoupper($matches[2]);
        }

        foreach ($candidates as $candidate) {
            if (isset($supported[$candidate])) {
                return $candidate;
            }

            if (
                is_array($aliases)
                && isset($aliases[$candidate])
                && is_string($aliases[$candidate])
                && isset($supported[$aliases[$candidate]])
            ) {
                return $aliases[$candidate];
            }
        }
    }

    return null;
}

/**
 * Suggest a builder locale from geolocation signals (non-binding hint for UI / auto mode).
 */
function breakdance_languages_suggest_locale_from_geolocation(): ?string
{
    $supported = breakdance_languages_supported_locale_codes();
    $country_map = breakdance_languages_geolocation_country_map();

    $freemius = function_exists('breakdance_languages_fs') ? breakdance_languages_fs() : null;

    if ($freemius !== null) {
        $user = $freemius->get_user();

        if (is_object($user) && isset($user->billing) && is_object($user->billing) && !empty($user->billing->address_country_code)) {
            $country = strtoupper((string) $user->billing->address_country_code);

            if (isset($country_map[$country])) {
                $locale = $country_map[$country];

                if (in_array($locale, $supported, true)) {
                    return $locale;
                }
            }
        }
    }

    $accept = breakdance_languages_locale_from_accept_language();

    if ($accept !== null) {
        return $accept;
    }

    $default = breakdance_languages_get_geolocation_config()['default'] ?? null;

    if (is_string($default) && in_array($default, $supported, true)) {
        return $default;
    }

    return null;
}

/**
 * Enrich locale fallbacks with Freemius geolocation aliases.
 *
 * @param array<string, string> $fallbacks
 * @return array<string, string>
 */
function breakdance_languages_merge_geolocation_locale_aliases(array $fallbacks): array
{
    $aliases = breakdance_languages_get_geolocation_config()['locale_aliases'] ?? [];

    if (!is_array($aliases)) {
        return $fallbacks;
    }

    foreach ($aliases as $alias => $target) {
        if (is_string($alias) && is_string($target)) {
            $fallbacks[$alias] = $target;
        }
    }

    return $fallbacks;
}
add_filter('breakdance_languages_locale_fallbacks', 'breakdance_languages_merge_geolocation_locale_aliases');

/**
 * When profile language is unset, geolocation can steer auto mode before English default.
 */
function breakdance_languages_resolve_locale_with_geolocation(?string $locale): ?string
{
    if ($locale !== null) {
        return $locale;
    }

    $preference = breakdance_languages_get_user_builder_locale();

    if ($preference !== BREAKDANCE_LANGUAGES_AUTO_LOCALE) {
        return null;
    }

    $wp_locale = breakdance_languages_get_unfiltered_user_locale();

    if ($wp_locale !== '' && $wp_locale !== 'en_US' && breakdance_languages_match_supported_locale($wp_locale) !== null) {
        return null;
    }

    return breakdance_languages_suggest_locale_from_geolocation();
}
add_filter('breakdance_languages_pre_resolve_locale', 'breakdance_languages_resolve_locale_with_geolocation');
