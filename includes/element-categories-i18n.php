<?php
/**
 * Builder Languages for Breakdance — Element categories i18n.
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

add_action('breakdance_loaded', 'breakdance_languages_translate_element_categories', 30);
add_action('plugins_loaded', 'breakdance_languages_translate_element_categories', 40);
add_action('init', 'breakdance_languages_translate_element_categories', 20);
add_filter('breakdance_languages_editor_label_dictionary', 'breakdance_languages_merge_element_category_labels', 9, 2);

/**
 * @return array<string, array<string, string>>
 */
function breakdance_languages_get_element_category_label_catalog(): array
{
    static $catalog = null;

    if (is_array($catalog)) {
        return $catalog;
    }

    $path = BREAKDANCE_LANGUAGES_PATH . 'config/element-category-labels.json';

    if (!is_readable($path)) {
        $catalog = [];

        return $catalog;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    $catalog = is_array($decoded) ? $decoded : [];

    return $catalog;
}

/**
 * @return array<string, string>
 */
function breakdance_languages_get_element_category_labels_for_locale(?string $locale = null): array
{
    if ($locale === null || $locale === '') {
        $locale = breakdance_languages_resolve_locale();
    }

    if ($locale === null || $locale === '') {
        return [];
    }

    $matched = breakdance_languages_match_supported_locale($locale) ?? $locale;
    $catalog = breakdance_languages_get_element_category_label_catalog();

    if (!isset($catalog[$matched]) || !is_array($catalog[$matched])) {
        return [];
    }

    /** @var array<string, string> $labels */
    $labels = [];

    foreach ($catalog[$matched] as $source => $target) {
        if (!is_string($source) || !is_string($target) || $source === '' || $target === '') {
            continue;
        }

        $labels[$source] = $target;
    }

    return $labels;
}

/**
 * Rewrite category labels in the Breakdance singleton before the builder loads.
 */
function breakdance_languages_translate_element_categories(): void
{
    if (!class_exists('\\Breakdance\\Elements\\ElementCategoriesController')) {
        return;
    }

    if (!function_exists('breakdance_languages_can_apply_translations')
        || !breakdance_languages_can_apply_translations()
    ) {
        return;
    }

    $labels = breakdance_languages_get_element_category_labels_for_locale();

    if ($labels === []) {
        return;
    }

    $controller = \Breakdance\Elements\ElementCategoriesController::getInstance();

    if (!isset($controller->categories) || !is_array($controller->categories)) {
        return;
    }

    foreach ($controller->categories as $index => $category) {
        if (!is_array($category)) {
            continue;
        }

        $label = isset($category['label']) && is_string($category['label'])
            ? $category['label']
            : '';

        if ($label === '' || !isset($labels[$label])) {
            continue;
        }

        $controller->categories[$index]['label'] = $labels[$label];
    }
}

/**
 * @param array<string, string> $dictionary
 * @return array<string, string>
 */
function breakdance_languages_merge_element_category_labels(array $dictionary, ?string $locale): array
{
    $labels = breakdance_languages_get_element_category_labels_for_locale($locale);

    if ($labels === []) {
        return $dictionary;
    }

    return array_merge($dictionary, $labels);
}
