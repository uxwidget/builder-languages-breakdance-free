<?php
/**
 * Builder Languages for Breakdance — Builder sync.
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

const BREAKDANCE_LANGUAGES_STORAGE_KEY = 'breakdance_languages_locale_changed';

/**
 * Current nonce for the Languages settings AJAX endpoints.
 */
function breakdance_languages_settings_ajax_nonce(): string
{
    return wp_create_nonce('breakdance_languages_save_locale');
}

/**
 * Whether the Languages settings AJAX nonce is valid.
 */
function breakdance_languages_verify_settings_ajax_nonce(): bool
{
    $nonce = isset($_POST['nonce'])
        ? sanitize_text_field(wp_unslash((string) $_POST['nonce']))
        : '';

    return wp_verify_nonce($nonce, 'breakdance_languages_save_locale') !== false;
}

/**
 * Shared nonce payload for Languages settings AJAX responses.
 *
 * @return array{nonce: string, breakdanceNonce: ?string}
 */
function breakdance_languages_settings_ajax_nonce_payload(): array
{
    $breakdance_nonce = null;

    if (function_exists('\Breakdance\AJAX\get_nonce_for_ajax_requests')) {
        $breakdance_nonce = \Breakdance\AJAX\get_nonce_for_ajax_requests();
    }

    return [
        'nonce' => breakdance_languages_settings_ajax_nonce(),
        'breakdanceNonce' => $breakdance_nonce,
    ];
}

/**
 * Reject a Languages settings AJAX request with a refreshable nonce.
 */
function breakdance_languages_reject_settings_ajax_nonce(): void
{
    wp_send_json_error(array_merge(
        [
            'message' => __('Your session has expired. Reload this page and try again.', 'breakdance-languages'),
            'code' => 'session_expired',
        ],
        breakdance_languages_settings_ajax_nonce_payload()
    ), 403);
}

/**
 * Register AJAX endpoint used by the Languages settings tab.
 */
add_action('wp_ajax_breakdance_languages_save_locale', 'breakdance_languages_ajax_save_locale');
add_action('wp_ajax_breakdance_languages_install_language_pack', 'breakdance_languages_ajax_install_language_pack');

/**
 * Save locale preference and return the merged builder JSON payload.
 */
function breakdance_languages_ajax_save_locale(): void
{
    if (!breakdance_languages_verify_settings_ajax_nonce()) {
        breakdance_languages_reject_settings_ajax_nonce();
    }

    if (!current_user_can('manage_options')) {
        wp_send_json_error(
            ['message' => __('Permission denied.', 'breakdance-languages')],
            403
        );
    }

    if (!breakdance_languages_is_licensed()) {
        wp_send_json_error(
            ['message' => __('Activate a valid license to use Builder Languages for Breakdance.', 'breakdance-languages')],
            403
        );
    }

    $selected = isset($_POST['locale'])
        ? sanitize_text_field(wp_unslash((string) $_POST['locale']))
        : BREAKDANCE_LANGUAGES_AUTO_LOCALE;

    if (
        $selected !== BREAKDANCE_LANGUAGES_AUTO_LOCALE
        && !in_array($selected, breakdance_languages_plan_locale_codes(), true)
    ) {
        wp_send_json_error(
            ['message' => __('The selected language is not available.', 'breakdance-languages')],
            400
        );
    }

    if (!breakdance_languages_set_user_builder_locale($selected)) {
        wp_send_json_error(array_merge(
            [
                'message' => __('Could not save the selected language.', 'breakdance-languages'),
            ],
            breakdance_languages_settings_ajax_nonce_payload()
        ), 500);
    }

    breakdance_languages_reload_php_textdomains();

    $resolved = breakdance_languages_resolve_locale();
    $json = breakdance_languages_build_i18n_json('{}', $resolved);
    $ui_strings = breakdance_languages_get_settings_ui_strings($selected);
    $language_pack = breakdance_languages_get_language_pack_context($selected);

    wp_send_json_success(array_merge(
        [
            'locale' => $resolved,
            'preference' => breakdance_languages_get_user_builder_locale(),
            'i18nJson' => $json,
            'message' => $ui_strings['success'],
            'strings' => $ui_strings,
            'profileContext' => breakdance_languages_get_profile_language_context(),
            'languagePack' => $language_pack,
        ],
        breakdance_languages_settings_ajax_nonce_payload()
    ));
}

/**
 * Install the WordPress core language pack for the selected builder locale.
 */
function breakdance_languages_ajax_install_language_pack(): void
{
    if (!breakdance_languages_verify_settings_ajax_nonce()) {
        breakdance_languages_reject_settings_ajax_nonce();
    }

    if (!current_user_can('manage_options')) {
        wp_send_json_error(
            ['message' => __('Permission denied.', 'breakdance-languages')],
            403
        );
    }

    $locale = isset($_POST['locale'])
        ? sanitize_text_field(wp_unslash((string) $_POST['locale']))
        : '';

    if (
        $locale === ''
        || $locale === BREAKDANCE_LANGUAGES_AUTO_LOCALE
        || !in_array($locale, breakdance_languages_plan_locale_codes(), true)
    ) {
        wp_send_json_error(
            ['message' => __('The selected language is not available.', 'breakdance-languages')],
            400
        );
    }

    $ui_strings = breakdance_languages_get_settings_ui_strings($locale);
    $install_result = breakdance_languages_install_core_language_pack_result($locale);

    if (!$install_result['success']) {
        $message = $ui_strings['wp_language_pack_install_error'];

        if ($install_result['message'] !== '') {
            $message .= ' ' . $install_result['message'];
        }

        wp_send_json_error(array_merge(
            [
                'message' => $message,
                'code' => $install_result['code'],
                'languagePack' => breakdance_languages_get_language_pack_context($locale),
            ],
            breakdance_languages_settings_ajax_nonce_payload()
        ), 500);
    }

    wp_send_json_success(array_merge(
        [
            'message' => sprintf($ui_strings['wp_language_pack_installed'], $ui_strings['locale_labels'][$locale] ?? $locale),
            'languagePack' => array_merge(
                breakdance_languages_get_language_pack_context($locale),
                ['just_installed' => true]
            ),
        ],
        breakdance_languages_settings_ajax_nonce_payload()
    ));
}

/**
 * Inject a builder listener that reloads when the locale changes elsewhere.
 */
add_action('unofficial_i_am_kevin_geary_master_of_all_things_css_and_html', 'breakdance_languages_print_builder_sync_script');

/**
 * Print the builder sync script on Breakdance builder pages.
 */
function breakdance_languages_print_builder_sync_script(): void
{
    if (!breakdance_languages_is_builder_page()) {
        return;
    }

    $storage_key = BREAKDANCE_LANGUAGES_STORAGE_KEY;
    ?>
    <script>
    (function () {
        var storageKey = <?php echo wp_json_encode($storage_key); ?>;

        function applyLocalePayload(payload) {
            if (!payload || !payload.i18nJson || !window.wp || !window.wp.i18n) {
                return false;
            }

            try {
                var translations = JSON.parse(payload.i18nJson);
                var localeData = translations.locale_data && (translations.locale_data.breakdance || translations.locale_data.messages);

                if (!localeData) {
                    return false;
                }

                localeData[''].domain = 'breakdance';
                window.wp.i18n.setLocaleData(localeData, 'breakdance');
                return true;
            } catch (error) {
                return false;
            }
        }

        function showReloadNotice(payload) {
            if (document.getElementById('bdl-locale-reload-notice')) {
                return;
            }

            var strings = (payload && payload.strings) || {};
            var title = strings.reload_notice_title || 'Builder language updated';
            var body = strings.reload_notice_body || 'Reload this page to apply translations in the element panels.';
            var reloadLabel = strings.reload_notice_reload || 'Reload now';
            var laterLabel = strings.reload_notice_later || 'Later';

            var notice = document.createElement('div');
            notice.id = 'bdl-locale-reload-notice';
            notice.setAttribute('role', 'status');
            notice.style.cssText = [
                'position:fixed',
                'top:16px',
                'inset-inline-end:16px',
                'z-index:999999',
                'max-width:360px',
                'padding:14px 16px',
                'border-radius:8px',
                'background:#1f2937',
                'color:#fff',
                'font:14px/1.4 -apple-system,BlinkMacSystemFont,Segoe UI,sans-serif',
                'box-shadow:0 10px 30px rgba(0,0,0,.25)',
                'text-align:start'
            ].join(';');

            var titleEl = document.createElement('strong');
            titleEl.style.cssText = 'display:block;margin-bottom:6px;';
            titleEl.textContent = title;

            var bodyEl = document.createElement('span');
            bodyEl.style.cssText = 'display:block;margin-bottom:12px;opacity:.9;';
            bodyEl.textContent = body;

            var reloadBtn = document.createElement('button');
            reloadBtn.type = 'button';
            reloadBtn.id = 'bdl-locale-reload-btn';
            reloadBtn.style.cssText = 'margin-inline-end:8px;padding:6px 12px;border:0;border-radius:6px;background:#3b82f6;color:#fff;cursor:pointer;';
            reloadBtn.textContent = reloadLabel;

            var dismissBtn = document.createElement('button');
            dismissBtn.type = 'button';
            dismissBtn.id = 'bdl-locale-reload-dismiss';
            dismissBtn.style.cssText = 'padding:6px 12px;border:1px solid rgba(255,255,255,.25);border-radius:6px;background:transparent;color:#fff;cursor:pointer;';
            dismissBtn.textContent = laterLabel;

            notice.appendChild(titleEl);
            notice.appendChild(bodyEl);
            notice.appendChild(reloadBtn);
            notice.appendChild(dismissBtn);
            document.body.appendChild(notice);

            reloadBtn.addEventListener('click', function () {
                window.location.reload();
            });

            dismissBtn.addEventListener('click', function () {
                notice.remove();
            });
        }

        window.addEventListener('storage', function (event) {
            if (event.key !== storageKey || !event.newValue) {
                return;
            }

            var payload = null;

            try {
                payload = JSON.parse(event.newValue);

                if (payload.forceReload) {
                    window.location.reload();
                    return;
                }

                applyLocalePayload(payload);
            } catch (error) {
                // Ignore malformed payloads.
            }

            showReloadNotice(payload);
        });
    }());
    </script>
    <?php
}

/**
 * Enqueue instant-save script on the Breakdance Languages settings tab.
 */
add_action('admin_enqueue_scripts', 'breakdance_languages_enqueue_settings_assets');

/**
 * @param string $hook_suffix
 */
function breakdance_languages_enqueue_settings_assets(string $hook_suffix): void
{
    unset($hook_suffix);

    if (!breakdance_languages_is_settings_admin_screen()) {
        return;
    }

    wp_enqueue_style(
        'breakdance-languages-settings-tab',
        plugins_url('admin/assets/settings-tab.css', BREAKDANCE_LANGUAGES_FILE),
        [],
        BREAKDANCE_LANGUAGES_VERSION
    );

    wp_enqueue_script(
        'breakdance-languages-settings-tab',
        plugins_url('admin/assets/settings-tab.js', BREAKDANCE_LANGUAGES_FILE),
        ['jquery'],
        BREAKDANCE_LANGUAGES_VERSION,
        true
    );

    wp_localize_script(
        'breakdance-languages-settings-tab',
        'breakdanceLanguagesSettings',
        [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => breakdance_languages_settings_ajax_nonce(),
            'storageKey' => BREAKDANCE_LANGUAGES_STORAGE_KEY,
            'autoLocale' => BREAKDANCE_LANGUAGES_AUTO_LOCALE,
            'isLicensed' => breakdance_languages_is_licensed(),
            'hasRealLicense' => breakdance_languages_freemius_has_active_license()
                && !breakdance_languages_is_freemius_dev_bypass_active(),
            'isDevBypass' => breakdance_languages_is_freemius_dev_bypass_active(),
            'isSandboxEnvironment' => breakdance_languages_is_freemius_sandbox_environment(),
            'freemiusReady' => breakdance_languages_freemius_is_configured(),
            'settingsPageSlug' => BREAKDANCE_LANGUAGES_SETTINGS_PAGE_SLUG,
            'stringsByLocale' => breakdance_languages_get_settings_ui_strings_by_locale(),
            'strings' => breakdance_languages_get_settings_ui_strings(
                breakdance_languages_get_user_builder_locale()
            ),
            'profileContext' => breakdance_languages_get_profile_language_context(),
            'languagePack' => breakdance_languages_get_language_pack_context(),
            'textDirection' => breakdance_languages_is_rtl_locale(
                breakdance_languages_resolve_settings_ui_locale()
            ) ? 'rtl' : 'ltr',
            'suggestedLocale' => breakdance_languages_suggest_locale_from_geolocation(),
        ]
    );
}

/**
 * Whether the current admin screen is the language settings page.
 *
 * @deprecated Use breakdance_languages_is_settings_admin_screen().
 */
function breakdance_languages_is_breakdance_settings_languages_screen(): bool
{
    return breakdance_languages_is_settings_admin_screen();
}
