<?php
/**
 * Builder Languages for Breakdance — Priority locale i18n.
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

add_filter('breakdance_languages_editor_label_dictionary', 'breakdance_languages_merge_priority_locale_labels', 11, 2);

/**
 * @return array<string, string>
 */
function breakdance_languages_get_priority_locale_strings(string $locale): array
{
    static $cache = [];

    if (isset($cache[$locale])) {
        return $cache[$locale];
    }

    $path = BREAKDANCE_LANGUAGES_PATH . 'config/' . $locale . '-priority-strings.json';

    if (!is_readable($path)) {
        $cache[$locale] = [];
        return $cache[$locale];
    }

    $decoded = json_decode((string) file_get_contents($path), true);

    if (!is_array($decoded)) {
        $cache[$locale] = [];
        return $cache[$locale];
    }

    unset($decoded['_comment']);
    $cache[$locale] = array_filter($decoded, 'is_string');

    return $cache[$locale];
}

/**
 * @param array<string, string> $dictionary
 * @return array<string, string>
 */
function breakdance_languages_merge_priority_locale_labels(array $dictionary, ?string $locale): array
{
    if ($locale === null || $locale === '') {
        return $dictionary;
    }

    $priority = breakdance_languages_get_priority_locale_strings($locale);

    if ($priority === []) {
        return $dictionary;
    }

    return array_merge($dictionary, $priority);
}

/**
 * Re-export for form-builder module (hi_IN legacy path).
 *
 * @return array<string, string>
 */
function breakdance_languages_get_hi_in_priority_strings(): array
{
    return breakdance_languages_get_priority_locale_strings('hi_IN');
}
