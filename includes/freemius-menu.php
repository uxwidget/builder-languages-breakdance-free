<?php
/**
 * Register Freemius admin pages under Breakdance.
 *
 * @package Breakdance_Languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'breakdance_languages_register_freemius_menu_placeholder', 50);
add_action('admin_menu', 'breakdance_languages_ensure_freemius_admin_pages_registered', 999999990);
add_action('admin_menu', 'breakdance_languages_fix_freemius_submenu_titles', PHP_INT_MAX);
add_action('admin_init', 'breakdance_languages_fix_freemius_submenu_titles', 0);
add_action('admin_init', 'breakdance_languages_set_freemius_admin_title', 1);

/**
 * Whether Freemius admin pages should be registered for this request.
 */
function breakdance_languages_should_register_freemius_pages(): bool
{
    return breakdance_languages_freemius_is_configured()
        && breakdance_languages_is_breakdance_active();
}

/**
 * Freemius overrides an existing submenu item during activation.
 * Breakdance does not ship this slug, so we register it after Breakdance menus load.
 */
function breakdance_languages_register_freemius_menu_placeholder(): void
{
    if (!breakdance_languages_should_register_freemius_pages()) {
        return;
    }

    breakdance_languages_register_freemius_admin_page(
        'breakdance-languages',
        __('License', 'breakdance-languages')
    );
}

/**
 * Re-register Freemius pages if another plugin removed submenu metadata.
 */
function breakdance_languages_ensure_freemius_admin_pages_registered(): void
{
    if (!breakdance_languages_should_register_freemius_pages()) {
        return;
    }

    breakdance_languages_register_freemius_admin_page(
        'breakdance-languages',
        __('License', 'breakdance-languages')
    );

    $freemius = breakdance_languages_fs();

    if (
        $freemius !== null &&
        method_exists($freemius, 'is_registered') &&
        $freemius->is_registered()
    ) {
        breakdance_languages_register_freemius_admin_page(
            'breakdance-languages-account',
            __('Account', 'breakdance-languages')
        );
    }

    breakdance_languages_attach_freemius_title_hooks('breakdance-languages');
    breakdance_languages_attach_freemius_title_hooks('breakdance-languages-account');
}

/**
 * Attach load hooks even when Freemius already registered the admin page.
 */
function breakdance_languages_attach_freemius_title_hooks(string $slug): void
{
    $parent = breakdance_languages_get_admin_parent_slug();

    foreach ([$parent, ''] as $parent_slug) {
        if (!breakdance_languages_is_freemius_page_registered($slug, $parent_slug)) {
            continue;
        }

        $hook = get_plugin_page_hookname($slug, $parent_slug);

        if ($hook !== '') {
            add_action('load-' . $hook, 'breakdance_languages_set_freemius_admin_title', 0);
        }
    }
}

/**
 * Register a Freemius admin page under Breakdance, with hidden-page fallback.
 */
function breakdance_languages_register_freemius_admin_page(string $slug, string $title): void
{
    $parent = breakdance_languages_get_admin_parent_slug();

    if (breakdance_languages_is_freemius_page_registered($slug, $parent)) {
        breakdance_languages_attach_freemius_title_hooks($slug);

        return;
    }

    $hook = add_submenu_page(
        $parent,
        $title,
        $title,
        'manage_options',
        $slug,
        'breakdance_languages_render_freemius_admin_page'
    );

    if (is_string($hook) && $hook !== '') {
        add_action('load-' . $hook, 'breakdance_languages_set_freemius_admin_title', 0);
        add_action('load-' . $slug, 'breakdance_languages_set_freemius_admin_title', 0);

        return;
    }

    if (breakdance_languages_is_freemius_page_registered($slug, '')) {
        breakdance_languages_attach_freemius_title_hooks($slug);

        return;
    }

    if (!class_exists('FS_Admin_Menu_Manager')) {
        return;
    }

    $hook = FS_Admin_Menu_Manager::add_subpage(
        '',
        $title,
        $title,
        'manage_options',
        $slug,
        'breakdance_languages_render_freemius_admin_page'
    );

    if (is_string($hook) && $hook !== '') {
        add_action('load-' . $hook, 'breakdance_languages_set_freemius_admin_title', 0);
        add_action('load-' . $slug, 'breakdance_languages_set_freemius_admin_title', 0);
    }
}

add_action('admin_menu', 'breakdance_languages_hide_freemius_license_submenu', 9999999999);

/**
 * Hide Freemius license/account submenu entries without unregistering their pages.
 */
function breakdance_languages_hide_freemius_license_submenu(): void
{
    if (!breakdance_languages_should_register_freemius_pages()) {
        return;
    }

    global $submenu;

    $parent = breakdance_languages_get_admin_parent_slug();
    $hidden_slugs = ['breakdance-languages', 'breakdance-languages-account'];

    if (!isset($submenu[$parent]) || !is_array($submenu[$parent])) {
        return;
    }

    foreach ($submenu[$parent] as $index => $item) {
        if (!is_array($item) || !isset($item[2])) {
            continue;
        }

        if (in_array((string) $item[2], $hidden_slugs, true)) {
            unset($submenu[$parent][$index]);
        }
    }
}

add_action('admin_head', 'breakdance_languages_hide_freemius_license_submenu_css');

/**
 * Fallback: hide Freemius license submenu links if they are re-added late.
 */
function breakdance_languages_hide_freemius_license_submenu_css(): void
{
    if (!breakdance_languages_should_register_freemius_pages()) {
        return;
    }

    echo '<style id="breakdance-languages-hide-freemius-menu">';
    echo '#toplevel_page_breakdance .wp-submenu a[href*="page=breakdance-languages"]:not([href*="settings"])';
    echo ',#toplevel_page_breakdance .wp-submenu a[href*="page=breakdance-languages-account"]';
    echo ',#toplevel_page_oxygen .wp-submenu a[href*="page=breakdance-languages"]:not([href*="settings"])';
    echo ',#toplevel_page_oxygen .wp-submenu a[href*="page=breakdance-languages-account"]';
    echo '{display:none!important;}';
    echo '</style>';
}

/**
 * Whether WordPress already registered the Freemius admin page hook.
 */
function breakdance_languages_is_freemius_page_registered(string $slug, string $parent): bool
{
    global $_registered_pages;

    if (!is_array($_registered_pages)) {
        return false;
    }

    $hookname = get_plugin_page_hookname($slug, $parent);

    return isset($_registered_pages[$hookname]);
}

/**
 * Ensure submenu metadata always has a page title (PHP 8.1+ admin-header.php).
 */
function breakdance_languages_fix_freemius_submenu_titles(): void
{
    global $submenu;

    if (!is_array($submenu)) {
        return;
    }

    $known_titles = [
        'breakdance-languages' => __('License', 'breakdance-languages'),
        'breakdance-languages-account' => __('Account', 'breakdance-languages'),
    ];

    foreach ($submenu as $parent => $items) {
        if (!is_array($items)) {
            continue;
        }

        foreach ($items as $index => $item) {
            if (!is_array($item) || !isset($item[2])) {
                continue;
            }

            if (strpos((string) $item[2], 'breakdance-languages') !== 0) {
                continue;
            }

            if ($item[2] === BREAKDANCE_LANGUAGES_SETTINGS_PAGE_SLUG) {
                continue;
            }

            $label = $known_titles[$item[2]] ?? __('License', 'breakdance-languages');
            $menu_label = isset($item[0]) && is_string($item[0]) && $item[0] !== '' ? $item[0] : $label;

            if (!isset($item[0]) || $item[0] === null || $item[0] === '') {
                $submenu[$parent][$index][0] = $label;
                $menu_label = $label;
            }

            if (!isset($item[3]) || $item[3] === null || $item[3] === '') {
                $submenu[$parent][$index][3] = $menu_label;
            }
        }
    }
}

/**
 * Ensure admin page title exists before admin-header.php runs.
 */
function breakdance_languages_set_freemius_admin_title(): void
{
    if (!breakdance_languages_is_freemius_admin_page()) {
        return;
    }

    global $title;

    $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash((string) $_GET['page'])) : '';

    if ($page === 'breakdance-languages-account') {
        $title = __('Account', 'breakdance-languages');
    } else {
        $title = __('License', 'breakdance-languages');
    }
}

/**
 * Whether the current request targets a Freemius admin screen for this plugin.
 */
function breakdance_languages_is_freemius_admin_page(): bool
{
    if (!is_admin()) {
        return false;
    }

    $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash((string) $_GET['page'])) : '';

    return in_array($page, ['breakdance-languages', 'breakdance-languages-account'], true);
}

/**
 * Render Freemius activation/account screens when Freemius did not override the hook.
 */
function breakdance_languages_render_freemius_admin_page(): void
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to access this page.', 'breakdance-languages'));
    }

    breakdance_languages_set_freemius_admin_title();

    $freemius = breakdance_languages_fs();

    if ($freemius === null) {
        wp_die(esc_html__('Freemius is not configured.', 'breakdance-languages'));
    }

    if (
        method_exists($freemius, 'is_activation_mode') &&
        $freemius->is_activation_mode() &&
        method_exists($freemius, '_connect_page_render')
    ) {
        $freemius->_connect_page_render();

        return;
    }

    if (method_exists($freemius, '_account_page_render')) {
        $freemius->_account_page_render();
    }
}
