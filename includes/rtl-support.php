<?php
/**
 * Builder Languages for Breakdance — RTL support.
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
 * Locales that render right-to-left in WordPress.
 *
 * @return list<string>
 */
function breakdance_languages_rtl_locale_codes(): array
{
    $codes = ['he_IL', 'ar'];

    /**
     * @var list<string> $codes
     */
    return apply_filters('breakdance_languages_rtl_locale_codes', $codes);
}

/**
 * Whether the active builder/admin locale is RTL.
 */
function breakdance_languages_is_rtl_locale(?string $locale = null): bool
{
    $locale = $locale ?: breakdance_languages_resolve_locale();

    if ($locale === null) {
        return is_rtl();
    }

    if (in_array($locale, breakdance_languages_rtl_locale_codes(), true)) {
        return true;
    }

    return function_exists('locale_is_rtl') ? locale_is_rtl($locale) : false;
}

/**
 * Mark Breakdance Languages admin screens for RTL-safe styling hooks.
 *
 * @param string $classes
 * @return string
 */
function breakdance_languages_admin_body_class(string $classes): string
{
    if (!breakdance_languages_is_rtl_locale(breakdance_languages_resolve_settings_ui_locale())) {
        return $classes;
    }

    return trim($classes . ' bdl-rtl-locale');
}
add_filter('admin_body_class', 'breakdance_languages_admin_body_class');
