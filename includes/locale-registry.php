<?php
/**
 * Builder Languages for Breakdance — Locale registry.
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
function breakdance_languages_get_locale_registry(): array
{
    static $registry = null;

    if ($registry !== null) {
        return $registry;
    }

    $path = BREAKDANCE_LANGUAGES_PATH . 'config/supported-locales.json';

    if (!is_readable($path)) {
        $registry = [];
        return $registry;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    $registry = is_array($decoded) ? $decoded : [];

    return $registry;
}

/**
 * @return list<string>
 */
function breakdance_languages_registry_locale_codes(): array
{
    $locales = breakdance_languages_get_locale_registry()['locales'] ?? [];

    if (!is_array($locales)) {
        return [];
    }

    return array_keys($locales);
}

/**
 * @return array<string, string>
 */
function breakdance_languages_registry_locale_labels(): array
{
    $locales = breakdance_languages_get_locale_registry()['locales'] ?? [];
    $labels = [];

    if (!is_array($locales)) {
        return $labels;
    }

    foreach ($locales as $code => $meta) {
        if (!is_array($meta)) {
            continue;
        }

        $label = $meta['label'] ?? $code;
        $labels[$code] = is_string($label) ? $label : (string) $code;
    }

    return $labels;
}

/**
 * @return list<string>
 */
function breakdance_languages_registry_editor_override_locales(): array
{
    $locales = breakdance_languages_get_locale_registry()['editor_override_locales'] ?? [];

    return is_array($locales) ? array_values(array_filter($locales, 'is_string')) : [];
}
