<?php
/**
 * Builder Languages for Breakdance — Free edition upsell (admin bar + banner).
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
 * Whether Free upsell chrome should render for the current user.
 */
function breakdance_languages_free_upsell_should_show(): bool
{
    if (!function_exists('breakdance_languages_is_free_edition') || !breakdance_languages_is_free_edition()) {
        return false;
    }

    return current_user_can('manage_options');
}

/**
 * Upgrade URL for Free → Pro.
 */
function breakdance_languages_free_upsell_checkout_url(): string
{
    if (function_exists('breakdance_languages_checkout_url')) {
        return breakdance_languages_checkout_url();
    }

    return 'https://uxwidget.com/builder-languages-breakdance/#pricing';
}

/**
 * Copy for Free upsell (EN / PT / ES via settings UI locale).
 *
 * @return array{title: string, lead: string, body: string, cta: string}
 */
function breakdance_languages_free_upsell_copy(): array
{
    $preference = function_exists('breakdance_languages_get_user_builder_locale')
        ? breakdance_languages_get_user_builder_locale()
        : 'en_US';

    $ui = function_exists('breakdance_languages_get_settings_ui_strings')
        ? breakdance_languages_get_settings_ui_strings($preference)
        : [];

    return [
        'title' => (string) ($ui['upsell_banner_title'] ?? 'Need more languages?'),
        'lead' => (string) ($ui['upsell_banner_lead'] ?? 'The Free version includes only PT-BR and ES-ES.'),
        'body' => (string) ($ui['upsell_banner_body'] ?? 'Switch to PRO and unlock 15 more languages (German, French, Arabic and many more) with updates and priority support.'),
        'cta' => (string) ($ui['upsell_banner_cta'] ?? $ui['license_upgrade'] ?? 'Get Pro Version'),
    ];
}

/**
 * Admin bar node.
 */
function breakdance_languages_free_upsell_admin_bar(\WP_Admin_Bar $bar): void
{
    if (!breakdance_languages_free_upsell_should_show()) {
        return;
    }

    $copy = breakdance_languages_free_upsell_copy();

    $bar->add_node([
        'id' => 'blb-free-upsell',
        'parent' => 'top-secondary',
        'title' => esc_html($copy['cta']),
        'href' => esc_url(breakdance_languages_free_upsell_checkout_url()),
        'meta' => [
            'class' => 'blb-free-upsell-ab',
            'title' => esc_attr($copy['title']),
        ],
    ]);
}
add_action('admin_bar_menu', 'breakdance_languages_free_upsell_admin_bar', 100);

/**
 * Enqueue Free upsell assets (admin + admin bar front when logged in).
 */
function breakdance_languages_free_upsell_enqueue_assets(): void
{
    if (!breakdance_languages_free_upsell_should_show()) {
        return;
    }

    $css = BREAKDANCE_LANGUAGES_PATH . 'admin/assets/free-upsell.css';

    if (!is_readable($css)) {
        return;
    }

    wp_enqueue_style(
        'breakdance-languages-free-upsell',
        plugins_url('admin/assets/free-upsell.css', BREAKDANCE_LANGUAGES_FILE),
        [],
        BREAKDANCE_LANGUAGES_VERSION
    );
}
add_action('admin_enqueue_scripts', 'breakdance_languages_free_upsell_enqueue_assets');
add_action('wp_enqueue_scripts', static function (): void {
    if (!is_admin_bar_showing()) {
        return;
    }

    breakdance_languages_free_upsell_enqueue_assets();
});

/**
 * Render the promo banner markup.
 */
function breakdance_languages_free_upsell_render_banner(string $context = 'notice'): void
{
    if (!breakdance_languages_free_upsell_should_show()) {
        return;
    }

    $copy = breakdance_languages_free_upsell_copy();
    $checkout = breakdance_languages_free_upsell_checkout_url();

    $class = $context === 'settings'
        ? 'blb-free-upsell-banner blb-free-upsell-banner--settings'
        : 'blb-free-upsell-banner notice';
    ?>
    <div class="<?php echo esc_attr($class); ?>" role="region" aria-label="<?php echo esc_attr($copy['title']); ?>">
        <div class="blb-free-upsell-banner__inner">
            <div class="blb-free-upsell-banner__copy">
                <strong class="blb-free-upsell-banner__title">
                    <span class="blb-free-upsell-banner__rocket" aria-hidden="true">🚀</span>
                    <?php echo esc_html($copy['title']); ?>
                </strong>
                <p class="blb-free-upsell-banner__lead"><?php echo esc_html($copy['lead']); ?></p>
                <p class="blb-free-upsell-banner__body"><?php echo esc_html($copy['body']); ?></p>
            </div>
            <div class="blb-free-upsell-banner__actions">
                <a class="button button-primary blb-free-upsell-banner__cta" href="<?php echo esc_url($checkout); ?>">
                    <?php echo esc_html($copy['cta']); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Dashboard / Breakdance screens notice.
 */
function breakdance_languages_free_upsell_admin_notice(): void
{
    if (!breakdance_languages_free_upsell_should_show()) {
        return;
    }

    if (!function_exists('get_current_screen')) {
        return;
    }

    $screen = get_current_screen();

    if ($screen === null) {
        return;
    }

    // Avoid double banner on Languages settings (upsell tip is inside the license card).
    if (
        function_exists('breakdance_languages_is_settings_admin_screen')
        && breakdance_languages_is_settings_admin_screen()
    ) {
        return;
    }

    $page = isset($_GET['page']) ? (string) wp_unslash($_GET['page']) : '';

    if (
        $page === 'breakdance-languages-settings'
        || $page === 'breakdance-languages'
        || (defined('BREAKDANCE_LANGUAGES_SETTINGS_PAGE_SLUG') && $page === BREAKDANCE_LANGUAGES_SETTINGS_PAGE_SLUG)
    ) {
        return;
    }

    $id = (string) $screen->id;
    $allowed = (
        $id === 'dashboard'
        || $id === 'plugins'
        || strpos($id, 'breakdance') !== false
    );

    if (!$allowed) {
        return;
    }

    breakdance_languages_free_upsell_render_banner('notice');
}
add_action('admin_notices', 'breakdance_languages_free_upsell_admin_notice');
