<?php
/**
 * Standalone admin menu pages under Breakdance.
 *
 * @package Breakdance_Languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const BREAKDANCE_LANGUAGES_SETTINGS_PAGE_SLUG = 'breakdance-languages-settings';

add_action('breakdance_admin_menu', 'breakdance_languages_register_settings_menu');

/**
 * Parent admin menu slug for the active Breakdance mode.
 */
function breakdance_languages_get_admin_parent_slug(): string
{
    if (defined('BREAKDANCE_MODE') && BREAKDANCE_MODE === 'oxygen') {
        return 'oxygen';
    }

    return 'breakdance';
}

/**
 * URL for the language settings page.
 */
function breakdance_languages_settings_page_url(): string
{
    return admin_url('admin.php?page=' . BREAKDANCE_LANGUAGES_SETTINGS_PAGE_SLUG);
}

/**
 * Register Breakdance → Languages.
 */
function breakdance_languages_register_settings_menu(): void
{
    if (!breakdance_languages_is_breakdance_active()) {
        return;
    }

    $ui_strings = breakdance_languages_get_settings_ui_strings(
        breakdance_languages_get_user_builder_locale()
    );

    add_submenu_page(
        breakdance_languages_get_admin_parent_slug(),
        __('Builder Languages for Breakdance', 'breakdance-languages'),
        $ui_strings['tab_title'],
        'manage_options',
        BREAKDANCE_LANGUAGES_SETTINGS_PAGE_SLUG,
        'breakdance_languages_render_settings_page'
    );
}

/**
 * Whether the current admin screen is the language settings page.
 */
function breakdance_languages_is_settings_admin_screen(): bool
{
    if (!is_admin()) {
        return false;
    }

    $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash((string) $_GET['page'])) : '';

    return $page === BREAKDANCE_LANGUAGES_SETTINGS_PAGE_SLUG;
}
