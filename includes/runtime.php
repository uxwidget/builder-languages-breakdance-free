<?php
/**
 * Builder Languages for Breakdance — Runtime.
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
 * Whether the current request is a Breakdance builder AJAX call.
 */
function breakdance_languages_is_breakdance_ajax_request(): bool
{
    if (isset($_POST['breakdance_ajax_at_any_url'])) {
        return breakdance_languages_freemius_is_builder_request();
    }

    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        return false;
    }

    return breakdance_languages_freemius_is_builder_request();
}

/**
 * Whether the plugin should skip admin-only bootstrap work.
 */
function breakdance_languages_should_skip_admin_bootstrap(): bool
{
    return breakdance_languages_is_breakdance_ajax_request()
        || breakdance_languages_freemius_is_builder_request();
}
