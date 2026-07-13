<?php
/**
 * Builder Languages for Breakdance — Locale.
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

const BREAKDANCE_LANGUAGES_USER_META = 'breakdance_languages_builder_locale';
const BREAKDANCE_LANGUAGES_AUTO_LOCALE = 'auto';

/**
 * Supported locale codes without triggering plugin textdomain loading.
 *
 * @return list<string>
 */
function breakdance_languages_supported_locale_codes(): array
{
    $codes = function_exists('breakdance_languages_registry_locale_codes')
        ? breakdance_languages_registry_locale_codes()
        : [
            'pt_BR',
            'pt_PT',
            'fr_FR',
            'de_DE',
            'es_ES',
            'es_LA',
            'ar',
            'ja_JP',
            'it_IT',
            'nl_NL',
            'hi_IN',
            'ru_RU',
            'zh_CN',
            'ko_KR',
            'pl_PL',
            'he_IL',
            'en_GB',
        ];

    /**
     * @var list<string> $codes
     */
    return apply_filters('breakdance_languages_supported_locale_codes', $codes);
}

/**
 * Human-readable locale labels for admin UI.
 *
 * @return array<string, string>
 */
function breakdance_languages_supported_locales(): array
{
    $labels = function_exists('breakdance_languages_registry_locale_labels')
        ? breakdance_languages_registry_locale_labels()
        : [
            'pt_BR' => 'Portuguese (Brazil)',
            'pt_PT' => 'Portuguese',
            'fr_FR' => 'French',
            'de_DE' => 'German',
            'es_ES' => 'Spanish',
            'es_LA' => 'Spanish (Latin America)',
            'ar' => 'Arabic',
            'ja_JP' => 'Japanese',
            'it_IT' => 'Italian',
            'nl_NL' => 'Dutch',
            'hi_IN' => 'Hindi',
            'ru_RU' => 'Russian',
            'zh_CN' => 'Chinese (Simplified)',
            'ko_KR' => 'Korean',
            'pl_PL' => 'Polish',
            'he_IL' => 'Hebrew',
            'en_GB' => 'English (International)',
        ];

    if (did_action('init')) {
        foreach ($labels as $locale => $label) {
            $labels[$locale] = __($label, 'breakdance-languages');
        }
    }

    /**
     * @var array<string, string> $labels
     */
    return apply_filters('breakdance_languages_supported_locales', $labels);
}

/**
 * Locale fallbacks used when an exact language code is missing or its
 * catalogue files are absent.
 *
 * @return array<string, string>
 */
function breakdance_languages_locale_fallbacks(): array
{
    $path = BREAKDANCE_LANGUAGES_PATH . 'translation-fallbacks.json';

    if (!is_readable($path)) {
        return [];
    }

    $fallbacks = json_decode((string) file_get_contents($path), true);
    $fallbacks = is_array($fallbacks) ? array_filter($fallbacks, 'is_string') : [];

    $registry = function_exists('breakdance_languages_get_locale_registry')
        ? breakdance_languages_get_locale_registry()
        : [];
    $aliases = $registry['locale_aliases'] ?? [];

    if (is_array($aliases)) {
        foreach ($aliases as $alias => $target) {
            if (is_string($alias) && is_string($target)) {
                $fallbacks[$alias] = $target;
            }
        }
    }

    /**
     * @var array<string, string> $fallbacks
     */
    return apply_filters('breakdance_languages_locale_fallbacks', $fallbacks);
}

/**
 * Whether the plugin ships readable catalogues for a locale.
 */
function breakdance_languages_locale_has_catalogues(string $locale): bool
{
    if ($locale === '') {
        return false;
    }

    $dir = BREAKDANCE_LANGUAGES_PATH . 'languages/';
    $stems = [
        'breakdance-' . $locale,
        'breakdance-elements-' . $locale,
        'breakdance-builder-' . $locale,
    ];

    foreach ($stems as $stem) {
        foreach (['.json', '.mo', '.po'] as $ext) {
            if (is_readable($dir . $stem . $ext)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Prefer a supported locale that actually has catalogue files.
 */
function breakdance_languages_prefer_locale_with_catalogues(string $locale): ?string
{
    $planLocales = breakdance_languages_runtime_locale_codes();

    if ($planLocales === []) {
        return null;
    }

    $candidates = [];

    if (in_array($locale, $planLocales, true)) {
        $candidates[] = $locale;
    }

    $fallbacks = breakdance_languages_locale_fallbacks();

    if (isset($fallbacks[$locale]) && is_string($fallbacks[$locale])) {
        $candidates[] = $fallbacks[$locale];
    }

    $seen = [];

    foreach ($candidates as $candidate) {
        if (isset($seen[$candidate]) || !in_array($candidate, $planLocales, true)) {
            continue;
        }

        $seen[$candidate] = true;

        if (breakdance_languages_locale_has_catalogues($candidate)) {
            return $candidate;
        }
    }

    return null;
}

/**
 * Get the builder locale preference saved for a user.
 */
function breakdance_languages_get_user_builder_locale(int $user_id = 0): string
{
    $user_id = $user_id > 0 ? $user_id : get_current_user_id();

    if ($user_id <= 0) {
        return BREAKDANCE_LANGUAGES_AUTO_LOCALE;
    }

    $value = get_user_meta($user_id, BREAKDANCE_LANGUAGES_USER_META, true);

    return is_string($value) && $value !== '' ? $value : BREAKDANCE_LANGUAGES_AUTO_LOCALE;
}

/**
 * Save the builder locale preference for a user.
 */
function breakdance_languages_set_user_builder_locale(string $locale, int $user_id = 0): bool
{
    $user_id = $user_id > 0 ? $user_id : get_current_user_id();

    if ($user_id <= 0) {
        return false;
    }

    if ($locale === BREAKDANCE_LANGUAGES_AUTO_LOCALE) {
        $existing = get_user_meta($user_id, BREAKDANCE_LANGUAGES_USER_META, true);

        if (!is_string($existing) || $existing === '') {
            return true;
        }

        return (bool) delete_user_meta($user_id, BREAKDANCE_LANGUAGES_USER_META);
    }

    $planLocales = breakdance_languages_plan_locale_codes();

    if (!in_array($locale, $planLocales, true)) {
        return false;
    }

    update_user_meta($user_id, BREAKDANCE_LANGUAGES_USER_META, $locale);

    return breakdance_languages_get_user_builder_locale($user_id) === $locale
        && breakdance_languages_sync_user_profile_locale($locale, $user_id);
}

/**
 * Keep the WordPress user profile locale aligned with an explicit builder choice.
 */
function breakdance_languages_sync_user_profile_locale(string $locale, int $user_id = 0): bool
{
    if ($locale === BREAKDANCE_LANGUAGES_AUTO_LOCALE) {
        return true;
    }

    $user_id = $user_id > 0 ? $user_id : get_current_user_id();

    if ($user_id <= 0) {
        return false;
    }

    if (!in_array($locale, breakdance_languages_supported_locale_codes(), true)) {
        return false;
    }

    $current = get_user_meta($user_id, 'locale', true);

    if (!is_string($current)) {
        $current = '';
    }

    if ($current === $locale) {
        return true;
    }

    $updated = update_user_meta($user_id, 'locale', $locale);

    if ($updated === false && get_user_meta($user_id, 'locale', true) !== $locale) {
        return false;
    }

    clean_user_cache($user_id);
    wp_cache_delete($user_id, 'user_meta');

    return get_user_meta($user_id, 'locale', true) === $locale;
}

/**
 * Resolve a locale code to a supported language pack for the active plan.
 * Only accepts a locale when catalogue files exist for it.
 */
function breakdance_languages_match_supported_locale(string $locale): ?string
{
    return breakdance_languages_prefer_locale_with_catalogues($locale);
}

/**
 * Resolve the active locale used by Breakdance Languages.
 */
function breakdance_languages_resolve_locale(?string $locale = null): ?string
{
    if (!breakdance_languages_can_apply_translations()) {
        return null;
    }

    if ($locale === null) {
        $preference = breakdance_languages_get_user_builder_locale();

        if ($preference !== BREAKDANCE_LANGUAGES_AUTO_LOCALE) {
            $matched = breakdance_languages_match_supported_locale($preference);

            if ($matched !== null) {
                return $matched;
            }
        }

        /**
         * @var string|null $geo
         */
        $geo = apply_filters('breakdance_languages_pre_resolve_locale', null);

        if (is_string($geo) && $geo !== '') {
            $matched = breakdance_languages_match_supported_locale($geo);

            if ($matched !== null) {
                return $matched;
            }
        }
    }

    $locale = $locale ?: breakdance_languages_get_unfiltered_user_locale();
    $matched = breakdance_languages_match_supported_locale($locale);

    if ($matched !== null) {
        return $matched;
    }

    // No plugin pack for this locale (e.g. en_US): leave Breakdance native English alone.
    return null;
}

/**
 * Read the WordPress profile locale without applying Breakdance overrides.
 */
function breakdance_languages_get_unfiltered_user_locale(int $user_id = 0): string
{
    remove_filter('locale', 'breakdance_languages_filter_locale_for_breakdance', 20);
    remove_filter('user_locale', 'breakdance_languages_filter_user_locale_for_breakdance', 20);

    $locale = $user_id > 0 ? get_user_locale($user_id) : get_user_locale();

    add_filter('locale', 'breakdance_languages_filter_locale_for_breakdance', 20);
    add_filter('user_locale', 'breakdance_languages_filter_user_locale_for_breakdance', 20, 2);

    return $locale;
}

/**
 * Context for the settings panel about WordPress profile vs builder language.
 *
 * @return array{
 *     wp_locale: string,
 *     wp_label: string,
 *     preference: string,
 *     effective_locale: ?string,
 *     effective_label: ?string,
 *     mismatch: bool,
 *     uses_profile_language: bool,
 *     profile_url: string
 * }
 */
function breakdance_languages_get_profile_language_context(): array
{
    $user_id = get_current_user_id();
    $preference = breakdance_languages_get_user_builder_locale();
    $labels = breakdance_languages_supported_locales();
    $stored_profile_locale = $user_id > 0 ? get_user_meta($user_id, 'locale', true) : '';
    $stored_profile_locale = is_string($stored_profile_locale) ? $stored_profile_locale : '';
    $wp_locale = $stored_profile_locale !== ''
        ? $stored_profile_locale
        : breakdance_languages_get_unfiltered_user_locale();
    $wp_matched = breakdance_languages_match_supported_locale($wp_locale);
    $wp_label = $wp_matched !== null
        ? ($labels[$wp_matched] ?? $wp_matched)
        : $wp_locale;

    $uses_profile_language = $preference === BREAKDANCE_LANGUAGES_AUTO_LOCALE;

    if ($uses_profile_language) {
        $effective = $wp_matched;
    } else {
        $effective = breakdance_languages_match_supported_locale($preference);
    }

    $effective_label = $effective !== null
        ? ($labels[$effective] ?? $effective)
        : null;

    $mismatch = !$uses_profile_language
        && $effective !== null
        && $wp_matched !== null
        && $wp_matched !== $effective;

    return [
        'wp_locale' => $wp_locale,
        'wp_matched' => $wp_matched,
        'wp_label' => $wp_label,
        'preference' => $preference,
        'effective_locale' => $effective,
        'effective_label' => $effective_label,
        'mismatch' => $mismatch,
        'uses_profile_language' => $uses_profile_language,
        'profile_url' => admin_url('profile.php'),
    ];
}

/**
 * Whether the active request should use the Breakdance builder locale.
 */
function breakdance_languages_should_apply_builder_locale(): bool
{
    if (!breakdance_languages_is_breakdance_active()) {
        return false;
    }

    if (breakdance_languages_is_builder_runtime_request()) {
        return true;
    }

    if (is_admin()) {
        return true;
    }

    return false;
}

/**
 * Align WordPress locale with the builder preference in Breakdance contexts.
 *
 * @param string $locale
 * @return string
 */
function breakdance_languages_filter_locale_for_breakdance(string $locale): string
{
    if (!breakdance_languages_should_apply_builder_locale()) {
        return $locale;
    }

    $resolved = breakdance_languages_resolve_locale($locale);

    return $resolved ?: $locale;
}
add_filter('locale', 'breakdance_languages_filter_locale_for_breakdance', 20);

/**
 * Align the user locale with the builder preference in Breakdance contexts.
 *
 * @param string $locale
 * @param int    $user_id
 * @return string
 */
function breakdance_languages_filter_user_locale_for_breakdance(string $locale, int $user_id): string
{
    if (!breakdance_languages_should_apply_builder_locale()) {
        return $locale;
    }

    if (!breakdance_languages_can_apply_translations()) {
        return $locale;
    }

    $preference = breakdance_languages_get_user_builder_locale($user_id);

    if ($preference !== BREAKDANCE_LANGUAGES_AUTO_LOCALE) {
        $matched = breakdance_languages_match_supported_locale($preference);

        if ($matched !== null) {
            return $matched;
        }
    }

    $resolved = breakdance_languages_resolve_locale($locale);

    return $resolved ?: $locale;
}
add_filter('user_locale', 'breakdance_languages_filter_user_locale_for_breakdance', 5, 2);

/**
 * Include Breakdance Languages locales in the profile language dropdown.
 *
 * WordPress marks unknown locales as "Site Default" when the core language pack
 * is not installed under wp-content/languages, even if user meta locale is set.
 *
 * @param string[] $languages
 * @return string[]
 */
function breakdance_languages_extend_available_languages(array $languages): array
{
    if (!is_admin() || !breakdance_languages_is_licensed()) {
        return $languages;
    }

    $plan_locales = breakdance_languages_plan_locale_codes();

    if ($plan_locales === []) {
        return $languages;
    }

    $extras = [];

    foreach ($plan_locales as $locale) {
        if (breakdance_languages_is_core_language_pack_installed($locale)) {
            $extras[] = $locale;
            continue;
        }

        $user_id = get_current_user_id();

        if ($user_id <= 0) {
            continue;
        }

        $profile_locale = get_user_meta($user_id, 'locale', true);
        $preference = breakdance_languages_get_user_builder_locale($user_id);

        if ($profile_locale === $locale || $preference === $locale) {
            $extras[] = $locale;
        }
    }

    return array_values(array_unique(array_merge($languages, $extras)));
}
add_filter('get_available_languages', 'breakdance_languages_extend_available_languages', 20);
