<?php
/**
 * Builder Languages for Breakdance — WP language pack.
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
 * Whether a WordPress core language pack is present on disk.
 */
function breakdance_languages_is_core_language_pack_installed(string $locale): bool
{
    if ($locale === '' || $locale === BREAKDANCE_LANGUAGES_AUTO_LOCALE || $locale === 'en_US') {
        return true;
    }

    $candidates = [
        WP_LANG_DIR . '/' . $locale . '.mo',
        WP_LANG_DIR . '/admin-' . $locale . '.mo',
        WP_LANG_DIR . '/' . $locale . '.l10n.php',
        WP_LANG_DIR . '/admin-' . $locale . '.l10n.php',
    ];

    foreach ($candidates as $file) {
        if (is_readable($file)) {
            return true;
        }
    }

    return false;
}

/**
 * Whether the current user can download WordPress core language packs.
 */
function breakdance_languages_can_install_core_language_pack(): bool
{
    if (!current_user_can('install_languages')) {
        return false;
    }

    if (!function_exists('wp_can_install_language_pack')) {
        require_once ABSPATH . 'wp-admin/includes/translation-install.php';
    }

    return wp_can_install_language_pack();
}

/**
 * Install a WordPress core language pack and return a structured result.
 *
 * @return array{
 *     success: bool,
 *     code: string,
 *     message: string
 * }
 */
function breakdance_languages_install_core_language_pack_result(string $locale): array
{
    if ($locale === '' || $locale === BREAKDANCE_LANGUAGES_AUTO_LOCALE || $locale === 'en_US') {
        return [
            'success' => true,
            'code' => 'already_available',
            'message' => '',
        ];
    }

    if (breakdance_languages_is_core_language_pack_installed($locale)) {
        return [
            'success' => true,
            'code' => 'already_installed',
            'message' => '',
        ];
    }

    if (!breakdance_languages_can_install_core_language_pack()) {
        return [
            'success' => false,
            'code' => 'no_permission',
            'message' => __('You do not have permission to install language packs.', 'breakdance-languages'),
        ];
    }

    if (!wp_is_file_mod_allowed('download_language_pack')) {
        return [
            'success' => false,
            'code' => 'file_mods_disabled',
            'message' => __('WordPress file modifications are disabled on this site.', 'breakdance-languages'),
        ];
    }

    if (!function_exists('wp_get_available_translations')) {
        require_once ABSPATH . 'wp-admin/includes/translation-install.php';
    }

    $translations = wp_get_available_translations();

    if (!is_array($translations) || $translations === []) {
        return [
            'success' => false,
            'code' => 'translations_api',
            'message' => __('Could not reach WordPress.org to download translations. Check your internet connection.', 'breakdance-languages'),
        ];
    }

    $translation = null;

    foreach ($translations as $item) {
        if (!is_array($item) || ($item['language'] ?? '') !== $locale) {
            continue;
        }

        $translation = (object) $item;
        break;
    }

    if ($translation === null) {
        return [
            'success' => false,
            'code' => 'locale_unavailable',
            'message' => __('This language is not available from WordPress.org.', 'breakdance-languages'),
        ];
    }

    if (!function_exists('WP_Filesystem')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    global $wp_filesystem;

    if (!is_object($wp_filesystem)) {
        $filesystem_ready = WP_Filesystem();

        if (!$filesystem_ready || !is_object($wp_filesystem)) {
            return [
                'success' => false,
                'code' => 'filesystem',
                'message' => __('WordPress could not access the filesystem to install the language pack.', 'breakdance-languages'),
            ];
        }
    }

    if (!class_exists('Language_Pack_Upgrader')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }

    remove_filter('get_available_languages', 'breakdance_languages_extend_available_languages', 20);

    $skin = new Automatic_Upgrader_Skin();
    $upgrader = new Language_Pack_Upgrader($skin);
    $translation->type = 'core';
    $result = $upgrader->upgrade($translation, ['clear_update_cache' => false]);

    add_filter('get_available_languages', 'breakdance_languages_extend_available_languages', 20);

    if (is_wp_error($result)) {
        return [
            'success' => false,
            'code' => 'upgrade_failed',
            'message' => $result->get_error_message(),
        ];
    }

    if (!$result) {
        $messages = method_exists($skin, 'get_upgrade_messages')
            ? array_filter((array) $skin->get_upgrade_messages())
            : [];

        return [
            'success' => false,
            'code' => 'upgrade_failed',
            'message' => $messages !== []
                ? implode(' ', $messages)
                : __('The language pack download failed.', 'breakdance-languages'),
        ];
    }

    if (!breakdance_languages_is_core_language_pack_installed($locale)) {
        return [
            'success' => false,
            'code' => 'verify_failed',
            'message' => __('The language pack was downloaded but the translation files were not found.', 'breakdance-languages'),
        ];
    }

    return [
        'success' => true,
        'code' => 'installed',
        'message' => '',
    ];
}

/**
 * Download and install a WordPress core language pack.
 */
function breakdance_languages_install_core_language_pack(string $locale): bool
{
    return breakdance_languages_install_core_language_pack_result($locale)['success'];
}

/**
 * Context for the settings panel about the WordPress core language pack.
 *
 * @return array{
 *     locale: string,
 *     label: string,
 *     installed: bool,
 *     can_install: bool,
 *     needs_notice: bool
 * }
 */
function breakdance_languages_get_language_pack_context(?string $locale = null): array
{
    if ($locale === null || $locale === '') {
        $locale = breakdance_languages_get_user_builder_locale();
    }

    if ($locale === BREAKDANCE_LANGUAGES_AUTO_LOCALE) {
        $locale = breakdance_languages_get_unfiltered_user_locale();
    }

    $matched = breakdance_languages_match_supported_locale($locale);
    $locale = $matched ?? $locale;
    $labels = breakdance_languages_supported_locales();
    $label = $labels[$locale] ?? $locale;
    $installed = breakdance_languages_is_core_language_pack_installed($locale);
    $can_install = breakdance_languages_can_install_core_language_pack();

    return [
        'locale' => $locale,
        'label' => $label,
        'installed' => $installed,
        'can_install' => $can_install,
        'needs_notice' => !$installed && $locale !== 'en_US',
    ];
}

/**
 * Try to install the WordPress core pack after a builder locale change.
 *
 * @return array{
 *     locale: string,
 *     label: string,
 *     installed: bool,
 *     can_install: bool,
 *     needs_notice: bool,
 *     just_installed: bool
 * }
 */
function breakdance_languages_sync_core_language_pack(string $locale): array
{
    $context = breakdance_languages_get_language_pack_context($locale);

    if (!$context['needs_notice']) {
        $context['just_installed'] = false;

        return $context;
    }

    if ($context['can_install'] && breakdance_languages_install_core_language_pack($locale)) {
        $context = breakdance_languages_get_language_pack_context($locale);
        $context['just_installed'] = true;

        return $context;
    }

    $context['just_installed'] = false;

    return $context;
}
