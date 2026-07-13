<?php
/**
 * Builder Languages for Breakdance — Shape dividers.
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

add_filter('breakdance_shape_dividers', 'breakdance_languages_translate_shape_dividers', 20);

/**
 * @return array<string, array<string, string>>
 */
function breakdance_languages_shape_divider_label_maps(): array
{
    static $maps = null;

    if ($maps !== null) {
        return $maps;
    }

    $path = BREAKDANCE_LANGUAGES_PATH . 'config/shape-divider-labels.json';

    if (!is_readable($path)) {
        $maps = [];

        return $maps;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    $maps = is_array($decoded) ? array_filter($decoded, 'is_array') : [];

    /**
     * @var array<string, array<string, string>> $maps
     */
    return apply_filters('breakdance_languages_shape_divider_label_maps', $maps);
}

/**
 * Labels for the active builder locale, if any.
 *
 * @return array<string, string>
 */
function breakdance_languages_get_shape_divider_labels(?string $locale = null): array
{
    if (!breakdance_languages_can_apply_translations()) {
        return [];
    }

    $locale = $locale ?: breakdance_languages_resolve_locale();

    if ($locale === null || in_array($locale, ['en_US', 'en_GB', 'en'], true)) {
        return [];
    }

    $maps = breakdance_languages_shape_divider_label_maps();

    return $maps[$locale] ?? [];
}

/**
 * Replace display labels only; preserve SVG payload values used by the builder.
 *
 * @param array<int, array<string, string>> $shapes
 * @return array<int, array<string, string>>
 */
function breakdance_languages_translate_shape_dividers(array $shapes): array
{
    $labels = breakdance_languages_get_shape_divider_labels();

    if ($labels === []) {
        return $shapes;
    }

    foreach ($shapes as $index => $shape) {
        if (!is_array($shape) || !isset($shape['text'], $shape['value'])) {
            continue;
        }

        if ($shape['value'] === 'custom') {
            continue;
        }

        $source = (string) $shape['text'];

        if (isset($labels[$source])) {
            $shapes[$index]['text'] = $labels[$source];
        }
    }

    return $shapes;
}
