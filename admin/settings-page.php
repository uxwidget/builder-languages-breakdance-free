<?php
/**
 * Builder Languages for Breakdance — Settings page.
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

add_action('admin_init', 'breakdance_languages_maybe_redirect_unlicensed_settings_to_freemius', 30);

/**
 * Whether Freemius itself reports an activated/paying install.
 *
 * On local DEV_MODE + product secret, skip the Freemius gate so Breakdance →
 * Languages stays usable for translation work. Production license keys still
 * fail against the sandbox API while WP_FS__DEV_MODE is on — that is expected.
 */
function breakdance_languages_has_freemius_license_ready(): bool
{
    if (breakdance_languages_is_freemius_dev_bypass_active()) {
        return true;
    }

    if (!breakdance_languages_freemius_is_configured()) {
        return false;
    }

    $freemius = breakdance_languages_fs();

    if ($freemius === null) {
        return false;
    }

    if (method_exists($freemius, 'can_use_premium_code') && $freemius->can_use_premium_code()) {
        return true;
    }

    if (
        method_exists($freemius, 'is_paying')
        && $freemius->is_paying()
    ) {
        return true;
    }

    return false;
}

/**
 * Without a Freemius license: Breakdance → Languages opens Freemius first.
 * After a valid Freemius license: stay on Languages (status panel).
 *
 * Local DEV_MODE + secret unlocks this gate (and translations). A live license
 * key only validates when WP_FS__DEV_MODE is off (production Freemius API).
 */
function breakdance_languages_maybe_redirect_unlicensed_settings_to_freemius(): void
{
    if (function_exists('breakdance_languages_is_free_edition') && breakdance_languages_is_free_edition()) {
        return;
    }

    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    if (!breakdance_languages_is_settings_admin_screen()) {
        return;
    }

    if (!breakdance_languages_freemius_is_configured()) {
        return;
    }

    if (breakdance_languages_has_freemius_license_ready()) {
        return;
    }

    // Optional escape hatch: ?blb_stay=1 keeps the settings screen while inactive.
    if (isset($_GET['blb_stay']) && (string) wp_unslash($_GET['blb_stay']) === '1') {
        return;
    }

    $target = breakdance_languages_license_manage_url();
    $settings = breakdance_languages_settings_page_url();

    if ($target === '' || $target === $settings) {
        return;
    }

    wp_safe_redirect($target);
    exit;
}

/**
 * Handle form submission for the language settings page.
 */
function breakdance_languages_handle_settings_page_save(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    if (!breakdance_languages_is_licensed()) {
        return;
    }

    $nonce_action = 'breakdance_languages_settings_page';
    $is_post = function_exists('\Breakdance\Util\is_post_request')
        ? \Breakdance\Util\is_post_request()
        : (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === 'POST');

    if (!$is_post || !check_admin_referer($nonce_action)) {
        return;
    }

    if (!isset($_POST['breakdance_languages_save'])) {
        return;
    }

    $selected = isset($_POST['breakdance_languages_builder_locale'])
        ? sanitize_text_field(wp_unslash((string) $_POST['breakdance_languages_builder_locale']))
        : BREAKDANCE_LANGUAGES_AUTO_LOCALE;

    if (
        $selected !== BREAKDANCE_LANGUAGES_AUTO_LOCALE
        && !in_array($selected, breakdance_languages_plan_locale_codes(), true)
    ) {
        add_settings_error(
            'breakdance_languages',
            'breakdance_languages_invalid_locale',
            __('The selected language is not available.', 'breakdance-languages'),
            'error'
        );
        return;
    }

    breakdance_languages_set_user_builder_locale($selected);
    breakdance_languages_reload_php_textdomains();

    add_settings_error(
        'breakdance_languages',
        'breakdance_languages_saved',
        __('Language saved successfully. Click "Refresh" to reload the page and apply the translation.', 'breakdance-languages'),
        'success'
    );
}

/**
 * Render Breakdance → Languages.
 */
function breakdance_languages_render_settings_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    breakdance_languages_handle_settings_page_save();
    settings_errors('breakdance_languages');

    $is_licensed = breakdance_languages_is_licensed();
    $preference = breakdance_languages_get_user_builder_locale();
    $nonce_action = 'breakdance_languages_settings_page';
    $ui_strings = breakdance_languages_get_settings_ui_strings($preference);
    $locale_labels = $ui_strings['locale_labels'];
    $plan_locale_codes = breakdance_languages_plan_locale_codes();
    $profile_context = breakdance_languages_get_profile_language_context();
    $license_context = breakdance_languages_get_license_panel_context();
    $language_pack_context = breakdance_languages_get_language_pack_context($preference);
    $license_status_key = 'license_status_' . $license_context['status'];
    $license_description_key = 'license_' . $license_context['status'] . '_description';
    $license_status_label = $ui_strings[$license_status_key] ?? $ui_strings['license_status_inactive'];
    $license_is_inactive = $license_context['status'] === 'inactive';
    $tip_label = $ui_strings['license_tip_label'] ?? 'TIP';
    $tip_body = $ui_strings['license_tip_body']
        ?? ($ui_strings['license_translation_scope_note'] ?? '');
    ?>
    <div class="wrap breakdance-languages-settings blb-settings">
        <h1 class="screen-reader-text"><?php echo esc_html__('Builder Languages for Breakdance', 'breakdance-languages'); ?></h1>

        <div class="blb-panel<?php echo $license_context['status'] === 'free' ? ' blb-panel--free' : ''; ?>">
            <?php if ($license_context['status'] === 'free') : ?>
            <span class="blb-ribbon-wrap" aria-hidden="true">
                <span id="breakdance-languages-free-ribbon" class="blb-ribbon"><?php echo esc_html($ui_strings['free_version_ribbon'] ?? 'VERSION FREE'); ?></span>
            </span>
            <?php endif; ?>
            <p id="breakdance-languages-brand-eyebrow" class="blb-panel__eyebrow">
                <?php echo esc_html($ui_strings['brand_eyebrow'] ?? 'BUILDER LANGUAGES for BREAKDANCE BUILDER'); ?>
            </p>
            <h2 id="breakdance-languages-panel-title" class="blb-panel__title">
                <?php echo esc_html($ui_strings['builder_language_label']); ?>
            </h2>

            <form action="" method="post" id="breakdance-languages-settings-form" class="blb-panel__form">
                <?php wp_nonce_field($nonce_action); ?>

                <label
                    for="breakdance_languages_builder_locale"
                    id="breakdance-languages-builder-language-label"
                    class="blb-field-label"
                >
                    <?php echo esc_html($ui_strings['header_alt'] ?? $ui_strings['builder_language_label']); ?>
                </label>
                <div class="breakdance-languages-locale-row">
                    <select
                        name="breakdance_languages_builder_locale"
                        id="breakdance_languages_builder_locale"
                        class="blb-select"
                        <?php disabled(!$is_licensed); ?>
                    >
                        <option value="<?php echo esc_attr(BREAKDANCE_LANGUAGES_AUTO_LOCALE); ?>" <?php selected($preference, BREAKDANCE_LANGUAGES_AUTO_LOCALE); ?>>
                            <?php echo esc_html($locale_labels['auto']); ?>
                        </option>
                        <?php foreach ($plan_locale_codes as $locale) : ?>
                            <option value="<?php echo esc_attr($locale); ?>" <?php selected($preference, $locale); ?>>
                                <?php echo esc_html($locale_labels[$locale] ?? $locale); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="breakdance-languages-locale-progress" hidden>
                    <div class="bdl-gemini-loader" aria-hidden="true">
                        <div
                            id="breakdance-languages-locale-progress-bar"
                            class="bdl-gemini-loader__bar bdl-gemini-loader__bar--determinate"
                            style="width: 0%;"
                        ></div>
                    </div>
                    <p class="bdl-gemini-loader__label">
                        <span id="breakdance-languages-locale-progress-text">
                            <?php echo esc_html($ui_strings['loading']); ?>
                        </span>
                        <span
                            id="breakdance-languages-locale-progress-percent"
                            class="bdl-gemini-loader__percent"
                            aria-hidden="true"
                        >0%</span>
                    </p>
                </div>
                <p id="breakdance-languages-save-status" class="blb-save-status" aria-live="polite"></p>
                <noscript>
                    <p>
                        <button type="submit" name="breakdance_languages_save" value="1" class="button button-primary blb-btn blb-btn--accent">
                            <?php echo esc_html__('Save language', 'breakdance-languages'); ?>
                        </button>
                    </p>
                </noscript>
            </form>

            <div
                id="breakdance-languages-profile-notice"
                class="blb-card<?php echo $profile_context['mismatch'] ? ' is-warning' : ''; ?>"
            >
                <h3 class="blb-card__title">
                    <span class="blb-card__dot" aria-hidden="true"></span>
                    <span id="breakdance-languages-profile-heading">
                        <?php echo esc_html($ui_strings['profile_language_heading']); ?>
                    </span>
                </h3>
                <p class="blb-card__body">
                    <span id="breakdance-languages-profile-current">
                        <?php
                        echo esc_html(
                            sprintf($ui_strings['profile_language_current'], $profile_context['wp_label'])
                        );
                        ?>
                    </span>
                    <?php echo ' '; ?>
                    <span id="breakdance-languages-profile-hint">
                        <?php
                        echo esc_html(
                            $profile_context['uses_profile_language']
                                ? $ui_strings['profile_language_auto_hint']
                                : $ui_strings['profile_language_align_hint']
                        );
                        ?>
                    </span>
                </p>
                <p
                    id="breakdance-languages-profile-mismatch"
                    class="blb-card__warning"
                    <?php echo $profile_context['mismatch'] ? '' : 'hidden'; ?>
                >
                    <?php
                    if ($profile_context['mismatch'] && $profile_context['effective_label'] !== null) {
                        echo esc_html(
                            sprintf(
                                $ui_strings['profile_language_mismatch'],
                                $profile_context['wp_label'],
                                $profile_context['effective_label']
                            )
                        );
                    }
                    ?>
                </p>
                <p class="blb-card__link-row">
                    <a id="breakdance-languages-profile-edit" class="blb-link" href="<?php echo esc_url($profile_context['profile_url']); ?>">
                        <?php echo esc_html($ui_strings['profile_language_edit']); ?>
                    </a>
                </p>
                <p id="breakdance-languages-profile-refresh-hint" class="blb-muted">
                    <?php echo esc_html($ui_strings['profile_language_refresh_hint'] ?? ''); ?>
                </p>
                <div
                    id="breakdance-languages-wp-language-pack"
                    class="breakdance-languages-wp-language-pack"
                    <?php echo $language_pack_context['needs_notice'] ? '' : 'hidden'; ?>
                >
                    <p id="breakdance-languages-wp-language-pack-message" class="blb-card__body">
                        <?php
                        if ($language_pack_context['needs_notice']) {
                            echo esc_html(
                                sprintf(
                                    $ui_strings['wp_language_pack_missing'],
                                    $language_pack_context['label']
                                )
                            );
                        }
                        ?>
                    </p>
                    <div id="breakdance-languages-wp-language-pack-progress" hidden>
                        <div class="bdl-gemini-loader" aria-hidden="true">
                            <div
                                id="breakdance-languages-wp-language-pack-progress-bar"
                                class="bdl-gemini-loader__bar bdl-gemini-loader__bar--determinate"
                                style="width: 0%;"
                            ></div>
                        </div>
                        <p class="bdl-gemini-loader__label">
                            <span id="breakdance-languages-wp-language-pack-progress-text">
                                <?php echo esc_html($ui_strings['wp_language_pack_installing']); ?>
                            </span>
                            <span id="breakdance-languages-wp-language-pack-progress-percent" class="bdl-gemini-loader__percent" aria-hidden="true">0%</span>
                        </p>
                    </div>
                </div>
                <div class="breakdance-languages-wp-language-pack-actions blb-actions">
                    <button
                        type="button"
                        class="button blb-btn blb-btn--danger"
                        id="breakdance-languages-install-language-pack"
                        <?php echo ($language_pack_context['needs_notice'] && $language_pack_context['can_install']) ? '' : 'hidden'; ?>
                    >
                        <?php echo esc_html($ui_strings['wp_language_pack_install_button']); ?>
                    </button>
                    <button
                        type="button"
                        class="button blb-btn blb-btn--accent"
                        id="breakdance-languages-profile-refresh"
                    >
                        <?php
                        echo esc_html(
                            $ui_strings['profile_language_refresh_button']
                                ?? ($ui_strings['wp_language_pack_reload'] ?? __('Refresh page', 'breakdance-languages'))
                        );
                        ?>
                    </button>
                    <span
                        id="breakdance-languages-wp-language-pack-manual"
                        class="blb-muted"
                        <?php echo ($language_pack_context['needs_notice'] && !$language_pack_context['can_install']) ? '' : 'hidden'; ?>
                    >
                        <?php echo esc_html($ui_strings['wp_language_pack_manual_hint']); ?>
                    </span>
                </div>
            </div>

            <div
                id="breakdance-languages-license-panel"
                class="blb-card blb-license-card<?php
                echo $license_is_inactive ? ' is-warning' : '';
                echo $license_context['status'] === 'dev' ? ' is-dev' : '';
                echo $license_context['status'] === 'free' ? ' is-free' : '';
                echo $license_context['environment_key'] === 'license_environment_sandbox' ? ' is-sandbox' : '';
                ?>"
                data-license-status="<?php echo esc_attr($license_context['status']); ?>"
            >
                <div class="blb-license__head" id="breakdance-languages-license-heading-row">
                    <div class="blb-license__title-group">
                        <h3 id="breakdance-languages-license-heading" class="blb-card__title blb-card__title--plain">
                            <?php
                            echo esc_html(
                                $license_context['status'] === 'free'
                                    ? ($ui_strings['license_heading_free'] ?? $ui_strings['license_heading'])
                                    : $ui_strings['license_heading']
                            );
                            ?>
                        </h3>
                        <?php if ($license_context['status'] === 'free') : ?>
                            <a
                                id="breakdance-languages-license-buy"
                                class="blb-link blb-license-upgrade-today"
                                href="<?php echo esc_url($license_context['checkout_url']); ?>"
                            >
                                <?php echo esc_html($ui_strings['license_upgrade_today'] ?? $ui_strings['license_upgrade'] ?? 'Upgrade today!'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <span
                        id="breakdance-languages-license-status-badge"
                        class="blb-status-badge<?php echo $license_is_inactive ? ' is-inactive' : ' is-active'; ?>"
                    >
                        <span class="blb-status-badge__dot" aria-hidden="true"></span>
                        <span id="breakdance-languages-license-status-heading" class="screen-reader-text">
                            <?php echo esc_html($ui_strings['license_status']); ?>
                        </span>
                        <strong id="breakdance-languages-license-status-label">
                            <?php echo esc_html($license_status_label); ?>
                        </strong>
                    </span>
                </div>
                <p
                    id="breakdance-languages-license-environment-row"
                    class="blb-muted"
                    <?php echo $license_context['show_environment'] ? '' : 'hidden'; ?>
                >
                    <span id="breakdance-languages-license-environment-heading">
                        <?php echo esc_html($ui_strings['license_environment']); ?>
                    </span>:
                    <strong id="breakdance-languages-license-environment-label">
                        <?php
                        if ($license_context['environment_key'] !== null) {
                            echo esc_html($ui_strings[$license_context['environment_key']] ?? '');
                        }
                        ?>
                    </strong>
                </p>
                <p id="breakdance-languages-license-description" class="blb-card__body">
                    <?php
                    echo esc_html(
                        $ui_strings[$license_description_key] ?? $ui_strings['license_inactive_description']
                    );
                    ?>
                </p>
                <div
                    id="breakdance-languages-license-scope-note"
                    class="blb-tip"
                    <?php echo $license_is_inactive ? 'hidden' : ''; ?>
                >
                    <strong id="breakdance-languages-license-tip-label" class="blb-tip__label">
                        <?php echo esc_html($tip_label); ?>
                    </strong>
                    <span id="breakdance-languages-license-tip-body" class="blb-tip__body">
                        <?php echo esc_html($tip_body); ?>
                    </span>
                </div>
                <?php if ($license_context['status'] === 'free') : ?>
                <div id="breakdance-languages-license-upsell-tip" class="blb-tip">
                    <strong id="breakdance-languages-upsell-tip-label" class="blb-tip__label">
                        <?php echo esc_html($ui_strings['upsell_banner_title'] ?? 'Need more languages?'); ?>
                    </strong>
                    <span id="breakdance-languages-upsell-tip-lead" class="blb-tip__lead">
                        <?php echo esc_html($ui_strings['upsell_banner_lead'] ?? 'The Free version includes only PT-BR and ES-ES.'); ?>
                    </span>
                    <span id="breakdance-languages-upsell-tip-body" class="blb-tip__body">
                        <?php echo esc_html($ui_strings['upsell_banner_body'] ?? ''); ?>
                    </span>
                </div>
                <?php endif; ?>
                <p
                    id="breakdance-languages-license-notice"
                    class="blb-card__warning"
                    <?php echo $license_is_inactive ? '' : 'hidden'; ?>
                >
                    <?php echo esc_html($ui_strings['license_notice']); ?>
                </p>
                <?php if ($license_context['status'] !== 'free') : ?>
                <p class="breakdance-languages-license-actions blb-license-actions">
                    <span id="breakdance-languages-license-account-heading" class="screen-reader-text">
                        <?php echo esc_html($ui_strings['license_account']); ?>
                    </span>
                    <a
                        id="breakdance-languages-license-manage"
                        class="blb-link"
                        href="<?php echo esc_url($license_context['account_url']); ?>"
                    >
                        <?php echo esc_html($ui_strings['license_manage']); ?>
                    </a>
                    <span
                        id="breakdance-languages-license-buy-separator"
                        class="breakdance-languages-license-actions__sep"
                        <?php echo $license_context['show_buy'] ? '' : 'hidden'; ?>
                    >
                        ·
                    </span>
                    <a
                        id="breakdance-languages-license-buy"
                        class="blb-link"
                        href="<?php echo esc_url($license_context['checkout_url']); ?>"
                        <?php echo $license_context['show_buy'] ? '' : 'hidden'; ?>
                    >
                        <?php echo esc_html($ui_strings['license_buy']); ?>
                    </a>
                </p>
                <?php else : ?>
                <span id="breakdance-languages-license-manage" hidden></span>
                <span id="breakdance-languages-license-buy-separator" hidden></span>
                <span id="breakdance-languages-license-account-heading" class="screen-reader-text"></span>
                <?php endif; ?>
            </div>
        </div>
        <p id="breakdance-languages-page-description" class="screen-reader-text">
            <?php echo esc_html($ui_strings['page_description']); ?>
        </p>
    </div>
    <?php
}
