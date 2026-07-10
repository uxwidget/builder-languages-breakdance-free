<?php
/**
 * Localized UI strings for the Languages settings tab.
 *
 * @package Breakdance_Languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * UI strings keyed by locale code.
 *
 * @return array<string, array<string, string>>
 */
function breakdance_languages_settings_ui_catalog(): array
{
    static $catalog = null;

    if (is_array($catalog)) {
        return $catalog;
    }

    $path = BREAKDANCE_LANGUAGES_PATH . 'config/settings-ui-strings.json';

    if (!is_readable($path)) {
        $catalog = [];

        return $catalog;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    $catalog = is_array($decoded) ? $decoded : [];

    return $catalog;
}

/**
 * Language names for the settings dropdown, keyed by UI locale.
 *
 * Native “auto” labels come from settings-ui-strings.json; product language
 * names fall back to the central registry (English labels) unless overridden.
 *
 * @return array<string, array<string, string>>
 */
function breakdance_languages_locale_labels_catalog(): array
{
    static $catalog = null;

    if (is_array($catalog)) {
        return $catalog;
    }

    $ui = breakdance_languages_settings_ui_catalog();
    $registry_labels = function_exists('breakdance_languages_registry_locale_labels')
        ? breakdance_languages_registry_locale_labels()
        : [];
    $catalog = [];

    foreach (array_keys($ui) as $locale) {
        $labels = $registry_labels;
        $auto = $ui[$locale]['locale_auto'] ?? ($ui['en_US']['locale_auto'] ?? 'Use WordPress profile language');
        $labels['auto'] = is_string($auto) ? $auto : 'Use WordPress profile language';

        // Prefer curated Portuguese labels when the UI itself is Portuguese.
        if ($locale === 'pt_BR' || $locale === 'pt_PT') {
            $labels = array_merge($labels, [
                'auto' => $auto,
                'pt_BR' => 'Português (Brasil)',
                'pt_PT' => 'Português',
                'fr_FR' => 'Francês',
                'de_DE' => 'Alemão',
                'es_ES' => 'Espanhol',
                'es_LA' => 'Espanhol (América Latina)',
                'ar' => 'Árabe',
                'ja_JP' => 'Japonês',
                'it_IT' => 'Italiano',
                'nl_NL' => 'Holandês',
                'hi_IN' => 'Hindi',
                'ru_RU' => 'Russo',
                'zh_CN' => 'Chinês (Simplificado)',
                'ko_KR' => 'Coreano',
                'pl_PL' => 'Polonês',
                'he_IL' => 'Hebraico',
                'en_GB' => 'Inglês (Internacional)',
                'en_US' => 'Inglês (Estados Unidos)',
            ]);
        }

        if ($locale === 'hi_IN') {
            $labels = array_merge($labels, [
                'auto' => $auto,
                'pt_BR' => 'पुर्तगाली (ब्राज़ील)',
                'pt_PT' => 'पुर्तगाली',
                'fr_FR' => 'फ़्रेंच',
                'de_DE' => 'जर्मन',
                'es_ES' => 'स्पेनिश',
                'es_LA' => 'स्पेनिश (लैटिन अमेरिका)',
                'ar' => 'अरबी',
                'ja_JP' => 'जापानी',
                'it_IT' => 'इतालवी',
                'nl_NL' => 'डच',
                'hi_IN' => 'हिन्दी',
                'ru_RU' => 'रूसी',
                'zh_CN' => 'चीनी (सरलीकृत)',
                'ko_KR' => 'कोरियाई',
                'pl_PL' => 'पोलिश',
                'he_IL' => 'हिब्रू',
                'en_GB' => 'अंग्रेज़ी (अंतरराष्ट्रीय)',
                'en_US' => 'अंग्रेज़ी (संयुक्त राज्य)',
            ]);
        }

        $catalog[$locale] = $labels;
    }

    if (!isset($catalog['en_US'])) {
        $catalog['en_US'] = array_merge($registry_labels, [
            'auto' => 'Use WordPress profile language',
        ]);
    }

    return $catalog;
}

/**
 * Resolve which locale should drive settings-tab UI copy.
 */
function breakdance_languages_resolve_settings_ui_locale(?string $preference = null): string
{
    $catalog = breakdance_languages_settings_ui_catalog();

    if ($preference === null || $preference === '') {
        $preference = breakdance_languages_get_user_builder_locale();
    }

    if ($preference === BREAKDANCE_LANGUAGES_AUTO_LOCALE) {
        $resolved = breakdance_languages_match_supported_locale(breakdance_languages_get_unfiltered_user_locale());

        return $resolved && isset($catalog[$resolved]) ? $resolved : 'en_US';
    }

    if (isset($catalog[$preference])) {
        return $preference;
    }

    $matched = breakdance_languages_match_supported_locale($preference);

    if ($matched !== null && isset($catalog[$matched])) {
        return $matched;
    }

    return 'en_US';
}

/**
 * Get settings-tab UI strings for a locale preference or code.
 *
 * @return array<string, mixed>
 */
function breakdance_languages_get_settings_ui_strings(?string $preference = null): array
{
    $catalog = breakdance_languages_settings_ui_catalog();
    $labels_catalog = breakdance_languages_locale_labels_catalog();
    $locale = breakdance_languages_resolve_settings_ui_locale($preference);
    $base = isset($catalog['en_US']) && is_array($catalog['en_US']) ? $catalog['en_US'] : [];
    $localized = isset($catalog[$locale]) && is_array($catalog[$locale]) ? $catalog[$locale] : [];
    /** @var array<string, string> $strings */
    $strings = array_merge($base, $localized);
    $native_labels = $labels_catalog[$locale] ?? ($labels_catalog['en_US'] ?? []);
    $supported_labels = breakdance_languages_supported_locales();
    $strings['locale_labels'] = array_merge($supported_labels, $native_labels);
    $strings['locale_labels']['auto'] = $native_labels['auto']
        ?? ($strings['locale_auto'] ?? 'Use WordPress profile language');

    return $strings;
}

/**
 * Build a locale => strings map for the settings tab script.
 *
 * @return array<string, array<string, mixed>>
 */
function breakdance_languages_get_settings_ui_strings_by_locale(): array
{
    $map = [];

    foreach (breakdance_languages_supported_locale_codes() as $locale) {
        $map[$locale] = breakdance_languages_get_settings_ui_strings($locale);
    }

    $map[BREAKDANCE_LANGUAGES_AUTO_LOCALE] = breakdance_languages_get_settings_ui_strings(
        BREAKDANCE_LANGUAGES_AUTO_LOCALE
    );

    return $map;
}
