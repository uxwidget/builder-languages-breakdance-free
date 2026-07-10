<?php
/**
 * Breakdance → Languages settings page.
 *
 * @package Breakdance_Languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
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
    ?>
    <div class="wrap breakdance-languages-settings">
        <h1><?php echo esc_html__('Builder Languages for Breakdance', 'breakdance-languages'); ?></h1>

        <p id="breakdance-languages-page-description">
            <?php echo esc_html($ui_strings['page_description']); ?>
        </p>

        <form action="" method="post" id="breakdance-languages-settings-form">
            <?php wp_nonce_field($nonce_action); ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="breakdance_languages_builder_locale" id="breakdance-languages-builder-language-label">
                                <?php echo esc_html($ui_strings['builder_language_label']); ?>
                            </label>
                        </th>
                        <td>
                            <div class="breakdance-languages-locale-row">
                                <select
                                    name="breakdance_languages_builder_locale"
                                    id="breakdance_languages_builder_locale"
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
                                <button
                                    type="button"
                                    class="button button-primary"
                                    id="breakdance-languages-force-reload"
                                >
                                    <?php echo esc_html($ui_strings['refresh']); ?>
                                </button>
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
                            <p id="breakdance-languages-save-status" class="description" aria-live="polite"></p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <noscript>
                <p>
                    <button type="submit" name="breakdance_languages_save" value="1" class="button button-primary">
                        <?php echo esc_html__('Save language', 'breakdance-languages'); ?>
                    </button>
                </p>
            </noscript>
        </form>

        <div
            id="breakdance-languages-profile-notice"
            class="breakdance-languages-profile-card<?php echo $profile_context['mismatch'] ? ' is-warning' : ''; ?>"
        >
            <p>
                <strong id="breakdance-languages-profile-heading">
                    <?php echo esc_html($ui_strings['profile_language_heading']); ?>
                </strong>
            </p>
            <p id="breakdance-languages-profile-current">
                <?php
                echo esc_html(
                    sprintf($ui_strings['profile_language_current'], $profile_context['wp_label'])
                );
                ?>
            </p>
            <p id="breakdance-languages-profile-hint">
                <?php
                echo esc_html(
                    $profile_context['uses_profile_language']
                        ? $ui_strings['profile_language_auto_hint']
                        : $ui_strings['profile_language_align_hint']
                );
                ?>
            </p>
            <p
                id="breakdance-languages-profile-mismatch"
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
            <p>
                <a id="breakdance-languages-profile-edit" href="<?php echo esc_url($profile_context['profile_url']); ?>">
                    <?php echo esc_html($ui_strings['profile_language_edit']); ?>
                </a>
            </p>
            <p id="breakdance-languages-profile-refresh-hint" class="description">
                <?php echo esc_html($ui_strings['profile_language_refresh_hint'] ?? ''); ?>
            </p>
            <div
                id="breakdance-languages-wp-language-pack"
                class="breakdance-languages-wp-language-pack"
                <?php echo $language_pack_context['needs_notice'] ? '' : 'hidden'; ?>
            >
                <p id="breakdance-languages-wp-language-pack-message">
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
                <p class="breakdance-languages-wp-language-pack-actions">
                    <button
                        type="button"
                        class="button button-secondary"
                        id="breakdance-languages-install-language-pack"
                        <?php echo $language_pack_context['can_install'] ? '' : 'hidden'; ?>
                    >
                        <?php echo esc_html($ui_strings['wp_language_pack_install_button']); ?>
                    </button>
                    <span
                        id="breakdance-languages-wp-language-pack-manual"
                        class="description"
                        <?php echo $language_pack_context['can_install'] ? 'hidden' : ''; ?>
                    >
                        <?php echo esc_html($ui_strings['wp_language_pack_manual_hint']); ?>
                    </span>
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
        </div>

        <div
            id="breakdance-languages-license-panel"
            class="breakdance-languages-profile-card breakdance-languages-license-card<?php
            echo $license_context['status'] === 'inactive' ? ' is-warning' : '';
            echo $license_context['status'] === 'dev' ? ' is-dev' : '';
            echo $license_context['environment_key'] === 'license_environment_sandbox' ? ' is-sandbox' : '';
            ?>"
        >
            <p>
                <strong id="breakdance-languages-license-heading">
                    <?php echo esc_html($ui_strings['license_heading']); ?>
                </strong>
            </p>
            <p>
                <span id="breakdance-languages-license-status-heading">
                    <?php echo esc_html($ui_strings['license_status']); ?>
                </span>:
                <strong id="breakdance-languages-license-status-label">
                    <?php echo esc_html($ui_strings[$license_status_key] ?? $ui_strings['license_status_inactive']); ?>
                </strong>
            </p>
            <p
                id="breakdance-languages-license-environment-row"
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
            <p id="breakdance-languages-license-description" class="description">
                <?php
                echo esc_html(
                    $ui_strings[$license_description_key] ?? $ui_strings['license_inactive_description']
                );
                ?>
            </p>
            <p
                id="breakdance-languages-license-notice"
                <?php echo $license_context['status'] === 'inactive' ? '' : 'hidden'; ?>
            >
                <?php echo esc_html($ui_strings['license_notice']); ?>
            </p>
            <p class="breakdance-languages-license-actions">
                <span id="breakdance-languages-license-account-heading" class="screen-reader-text">
                    <?php echo esc_html($ui_strings['license_account']); ?>
                </span>
                <a
                    id="breakdance-languages-license-manage"
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
                    href="<?php echo esc_url($license_context['checkout_url']); ?>"
                    <?php echo $license_context['show_buy'] ? '' : 'hidden'; ?>
                >
                    <?php echo esc_html($ui_strings['license_buy']); ?>
                </a>
            </p>
        </div>
    </div>
    <?php
}
