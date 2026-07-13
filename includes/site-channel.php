<?php
/**
 * Builder Languages for Breakdance — Local site channel badge.
 *
 * Distinguishes sales packaging (blb01) from i18n/dev (sparklean) in the admin bar.
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
 * Local workflow channel: sales | dev | unknown.
 *
 * Prefer wp-config:
 *   define( 'BREAKDANCE_LANGUAGES_CHANNEL', 'sales' ); // blb01
 *   define( 'BREAKDANCE_LANGUAGES_CHANNEL', 'dev' );   // sparklean
 */
function breakdance_languages_get_site_channel(): string
{
    if (defined('BREAKDANCE_LANGUAGES_CHANNEL')) {
        $channel = strtolower(trim((string) BREAKDANCE_LANGUAGES_CHANNEL));

        if (in_array($channel, ['sales', 'dev'], true)) {
            return $channel;
        }
    }

    $host = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';
    $host_only = preg_replace('/:\d+$/', '', $host) ?? $host;

    if (
        strpos($host_only, 'blb01') !== false
        || strpos($host_only, 'blb-venda') !== false
        || strpos($host_only, 'builder-languages-store') !== false
    ) {
        return 'sales';
    }

    if (
        strpos($host_only, 'sparklean') !== false
        || strpos($host_only, '-dev') !== false
    ) {
        return 'dev';
    }

    return 'unknown';
}

/**
 * Whether the admin-bar channel badge should appear (local only, never release ZIP).
 *
 * Hide for marketplace screenshots:
 *   define( 'BREAKDANCE_LANGUAGES_HIDE_CHANNEL_BADGE', true );
 * or open admin with ?blb_hide_channel=1
 */
function breakdance_languages_should_show_site_channel_badge(): bool
{
    if (defined('BREAKDANCE_LANGUAGES_RELEASE_BUILD') && BREAKDANCE_LANGUAGES_RELEASE_BUILD) {
        return false;
    }

    if (defined('BREAKDANCE_LANGUAGES_HIDE_CHANNEL_BADGE') && BREAKDANCE_LANGUAGES_HIDE_CHANNEL_BADGE) {
        return false;
    }

    if (isset($_GET['blb_hide_channel']) && (string) $_GET['blb_hide_channel'] === '1') {
        return false;
    }

    if (!function_exists('breakdance_languages_is_local_dev_site')) {
        return false;
    }

    return breakdance_languages_is_local_dev_site() && is_admin();
}

add_action('admin_bar_menu', static function ($bar): void {
    if (!($bar instanceof WP_Admin_Bar) || !breakdance_languages_should_show_site_channel_badge()) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    $channel = breakdance_languages_get_site_channel();
    $version = defined('BREAKDANCE_LANGUAGES_VERSION')
        ? (string) BREAKDANCE_LANGUAGES_VERSION
        : '';

    $labels = [
        'sales' => 'BLB · Sales',
        'dev' => 'BLB · Dev',
        'unknown' => 'BLB · Local',
    ];

    $title = $labels[$channel] ?? $labels['unknown'];

    if ($version !== '') {
        $title .= ' · ' . $version;
    }

    $bar->add_node([
        'id' => 'breakdance-languages-channel',
        'title' => esc_html($title),
        'href' => false,
        'meta' => [
            'title' => $channel === 'sales'
                ? 'Site de embalagem / Freemius comercial (não misturar com sparklean)'
                : ($channel === 'dev'
                    ? 'Site de desenvolvimento / traduções'
                    : 'Defina BREAKDANCE_LANGUAGES_CHANNEL no wp-config (sales|dev)'),
        ],
    ]);
}, 100);
