(function ($) {
    'use strict';

    if (typeof breakdanceLanguagesSettings === 'undefined') {
        return;
    }

    var settings = breakdanceLanguagesSettings;
    var $select = $('#breakdance_languages_builder_locale');
    var $profileRefreshButton = $('#breakdance-languages-profile-refresh');
    var $status = $('#breakdance-languages-save-status');
    var savedLocale = $select.val();
    var activeRequest = null;

    function createDeterminateProgress(config) {
        var $container = $(config.container);
        var $bar = $(config.bar);
        var $text = $(config.text);
        var $percent = $(config.percent);
        var phaseKeys = config.phases || [];
        var timer = null;
        var value = 0;

        function getPhaseLabel(percent, strings) {
            var key;

            if (percent < 25) {
                key = phaseKeys[0];
            } else if (percent < 70) {
                key = phaseKeys[1];
            } else if (percent < 100) {
                key = phaseKeys[2];
            } else {
                key = phaseKeys[3];
            }

            return (key && strings[key]) || (phaseKeys[0] && strings[phaseKeys[0]]) || '';
        }

        function set(percent, locale) {
            var strings = getStringsForLocale(locale);
            var safePercent = Math.max(0, Math.min(100, Math.round(percent)));

            value = safePercent;

            if ($bar.length) {
                $bar.css('width', safePercent + '%');
            }

            if ($percent.length) {
                $percent.text(safePercent + '%');
            }

            if ($text.length) {
                $text.text(getPhaseLabel(safePercent, strings));
            }
        }

        function start(locale) {
            stop();

            if ($container.length) {
                $container.removeAttr('hidden');
            }

            set(8, locale);

            timer = window.setInterval(function () {
                if (value >= 92) {
                    return;
                }

                var increment = value < 40 ? 4 : (value < 75 ? 2 : 1);
                set(value + increment, locale);
            }, 450);
        }

        function stop() {
            if (timer !== null) {
                window.clearInterval(timer);
                timer = null;
            }
        }

        function hide(locale, finalPercent) {
            stop();

            if (typeof finalPercent === 'number') {
                set(finalPercent, locale);
            }

            if ($container.length) {
                $container.attr('hidden', 'hidden');
            }

            set(0, locale);
        }

        return {
            set: set,
            start: start,
            stop: stop,
            hide: hide
        };
    }

    var localeProgress = createDeterminateProgress({
        container: '#breakdance-languages-locale-progress',
        bar: '#breakdance-languages-locale-progress-bar',
        text: '#breakdance-languages-locale-progress-text',
        percent: '#breakdance-languages-locale-progress-percent',
        phases: ['loading', 'loading_translations', 'loading_finalizing', 'success']
    });

    var languagePackProgress = createDeterminateProgress({
        container: '#breakdance-languages-wp-language-pack-progress',
        bar: '#breakdance-languages-wp-language-pack-progress-bar',
        text: '#breakdance-languages-wp-language-pack-progress-text',
        percent: '#breakdance-languages-wp-language-pack-progress-percent',
        phases: [
            'wp_language_pack_installing',
            'wp_language_pack_downloading',
            'wp_language_pack_finalizing',
            'wp_language_pack_installed_short'
        ]
    });

    function getStringsForLocale(locale) {
        if (settings.stringsByLocale && settings.stringsByLocale[locale]) {
            return settings.stringsByLocale[locale];
        }

        return settings.strings || {};
    }

    function applySettingsNonce(data) {
        if (data && data.nonce) {
            settings.nonce = data.nonce;
        }
    }

    function broadcastBreakdanceNonce(nonce) {
        if (!nonce || typeof window.BroadcastChannel === 'undefined') {
            return;
        }

        try {
            var channel = new BroadcastChannel('breakdance');
            channel.postMessage({
                event: 'nonceRefresh',
                nonce: nonce
            });
            channel.close();
        } catch (error) {
            // Ignore cross-tab nonce refresh failures.
        }
    }

    function getAjaxErrorMessage(xhr, locale) {
        var strings = getStringsForLocale(locale);
        var message = strings.error;
        var data = xhr.responseJSON && xhr.responseJSON.data;

        if (data) {
            applySettingsNonce(data);

            if (data.code === 'session_expired') {
                return strings.session_expired || data.message || message;
            }

            if (data.message) {
                return data.message;
            }
        }

        if (
            xhr.status === 403
            && (xhr.responseText === '-1' || xhr.responseText === '0' || xhr.responseText === '')
        ) {
            return strings.session_expired || message;
        }

        return message;
    }

    function setStatus(message, type) {
        if (!$status.length) {
            return;
        }

        $status
            .removeClass('is-success is-error')
            .addClass(type ? 'is-' + type : '')
            .text(message || '');
    }

    function updatePageCopy(locale) {
        var strings = getStringsForLocale(locale);

        $('#breakdance-languages-page-description').text(strings.page_description || '');
        $('#breakdance-languages-brand-eyebrow').text(
            strings.brand_eyebrow || 'BUILDER LANGUAGES for BREAKDANCE BUILDER'
        );
        $('#breakdance-languages-panel-title').text(strings.builder_language_label || '');
        $('#breakdance-languages-builder-language-label').text(
            strings.header_alt || strings.builder_language_label || ''
        );
        if ($('#breakdance-languages-free-ribbon').length) {
            $('#breakdance-languages-free-ribbon').text(strings.free_version_ribbon || 'VERSION FREE');
        }
    }

    function updateSelectLabels(locale) {
        var strings = getStringsForLocale(locale);
        var labels = strings.locale_labels || {};

        $select.find('option').each(function () {
            var value = $(this).val();

            if (labels[value]) {
                $(this).text(labels[value]);
            }
        });
    }

    function updateLicensePanel(locale) {
        var strings = getStringsForLocale(locale);
        var statusLabel;
        var statusDescription;
        var isInactive = false;

        if (!settings.freemiusReady) {
            statusLabel = strings.license_status_dev;
            statusDescription = strings.license_dev_description;
        } else if (settings.isDevBypass) {
            statusLabel = strings.license_status_dev;
            statusDescription = strings.license_dev_description;
        } else if (settings.isFreeEdition) {
            statusLabel = strings.license_status_active || strings.license_status_free;
            statusDescription = strings.license_free_description;
        } else if (settings.hasRealLicense) {
            statusLabel = strings.license_status_active;
            statusDescription = strings.license_active_description;
        } else if (!settings.isLicensed) {
            statusLabel = strings.license_status_inactive;
            statusDescription = strings.license_inactive_description;
            isInactive = true;
        } else {
            statusLabel = strings.license_status_active;
            statusDescription = strings.license_active_description;
        }

        var $panel = $('#breakdance-languages-license-panel');
        var $badge = $('#breakdance-languages-license-status-badge');

        if (settings.isFreeEdition) {
            $('#breakdance-languages-license-heading').text(
                strings.license_heading_free || strings.license_heading || ''
            );
            $('#breakdance-languages-license-buy')
                .text(strings.license_upgrade_today || strings.license_upgrade || 'Upgrade today!')
                .prop('hidden', false)
                .attr('href', settings.checkoutUrl || $('#breakdance-languages-license-buy').attr('href'));
        } else {
            $('#breakdance-languages-license-heading').text(strings.license_heading || '');
        }

        $('#breakdance-languages-license-status-heading').text(strings.license_status || '');
        $('#breakdance-languages-license-status-label').text(statusLabel || '');
        $('#breakdance-languages-license-description').text(statusDescription || '');
        $('#breakdance-languages-license-account-heading').text(strings.license_account || '');
        $('#breakdance-languages-license-manage').text(strings.license_manage || '');

        if ($('#breakdance-languages-license-scope-note').length) {
            var tipLabel = strings.license_tip_label || '';
            var tipBody = strings.license_tip_body || strings.license_translation_scope_note || '';
            var hasTip = !!(tipLabel || tipBody);

            $('#breakdance-languages-license-tip-label').text(tipLabel);
            $('#breakdance-languages-license-tip-body').text(tipBody);
            $('#breakdance-languages-license-scope-note').prop('hidden', isInactive || !hasTip);
        }

        var $upsellTip = $('#breakdance-languages-license-upsell-tip');

        if ($upsellTip.length) {
            $upsellTip.find('.blb-tip__label').text(strings.upsell_banner_title || '');
            $upsellTip.find('.blb-tip__lead').text(strings.upsell_banner_lead || '');
            $upsellTip.find('.blb-tip__body').text(strings.upsell_banner_body || '');
        }

        if ($badge.length) {
            $badge
                .toggleClass('is-inactive', isInactive)
                .toggleClass('is-active', !isInactive)
                .toggleClass('is-dev', statusLabel === strings.license_status_dev);
        }

        if (!settings.isFreeEdition) {
            $('#breakdance-languages-license-buy').text(strings.license_buy || '');
        }

        var environmentLabel = '';

        if (settings.isDevBypass) {
            environmentLabel = strings.license_environment_dev || '';
        } else if (settings.isSandboxEnvironment) {
            environmentLabel = strings.license_environment_sandbox || '';
        } else if (settings.hasRealLicense) {
            environmentLabel = strings.license_environment_production || '';
        }

        $('#breakdance-languages-license-environment-heading').text(strings.license_environment || '');
        $('#breakdance-languages-license-environment-label').text(environmentLabel);

        if ($('#breakdance-languages-license-environment-row').length) {
            $('#breakdance-languages-license-environment-row').prop('hidden', environmentLabel === '');
        }

        if ($('#breakdance-languages-license-notice').length) {
            $('#breakdance-languages-license-notice')
                .text(strings.license_notice || '')
                .prop('hidden', !isInactive);
        }

        if ($('#breakdance-languages-license-buy').length) {
            var showBuy = (isInactive && settings.freemiusReady) || !!settings.isFreeEdition;

            $('#breakdance-languages-license-buy').prop('hidden', !showBuy);
            if ($('#breakdance-languages-license-buy-separator').length) {
                $('#breakdance-languages-license-buy-separator').prop('hidden', !showBuy || !!settings.isFreeEdition);
            }
        }

        if ($panel.length) {
            $panel.toggleClass('is-warning', isInactive);
            $panel.toggleClass('is-dev', statusLabel === strings.license_status_dev);
            $panel.toggleClass(
                'is-sandbox',
                environmentLabel === (strings.license_environment_sandbox || '')
            );
        }
    }

    function updateProfileNotice(selectedLocale) {
        var $notice = $('#breakdance-languages-profile-notice');

        if (!$notice.length || !settings.profileContext) {
            return;
        }

        var strings = getStringsForLocale(selectedLocale);
        var ctx = settings.profileContext;
        var isAuto = selectedLocale === settings.autoLocale;
        var effectiveLabel = isAuto
            ? ctx.wp_label
            : ((strings.locale_labels && strings.locale_labels[selectedLocale]) || selectedLocale);
        var mismatch = !isAuto && ctx.wp_matched && selectedLocale !== ctx.wp_matched;

        $('#breakdance-languages-profile-heading').text(strings.profile_language_heading || '');
        $('#breakdance-languages-profile-current').text(
            (strings.profile_language_current || 'Your profile is set to: %s').replace('%s', ctx.wp_label)
        );
        $('#breakdance-languages-profile-hint').text(
            isAuto
                ? (strings.profile_language_auto_hint || '')
                : (strings.profile_language_align_hint || '')
        );

        var $mismatch = $('#breakdance-languages-profile-mismatch');

        if (mismatch) {
            $mismatch
                .text(
                    (strings.profile_language_mismatch || '')
                        .replace('%1$s', ctx.wp_label)
                        .replace('%2$s', effectiveLabel)
                )
                .removeAttr('hidden');
            $notice.addClass('is-warning');
        } else {
            $mismatch.attr('hidden', 'hidden').text('');
            $notice.removeClass('is-warning');
        }

        $('#breakdance-languages-profile-edit').text(strings.profile_language_edit || '');
        $('#breakdance-languages-profile-refresh-hint').text(strings.profile_language_refresh_hint || '');
        if ($profileRefreshButton.length) {
            $profileRefreshButton.text(
                strings.profile_language_refresh_button
                    || strings.wp_language_pack_reload
                    || strings.refresh
                    || ''
            );
        }
    }

    function updateLanguagePackNotice(selectedLocale, packContext) {
        var $panel = $('#breakdance-languages-wp-language-pack');
        var $button = $('#breakdance-languages-install-language-pack');
        var $manual = $('#breakdance-languages-wp-language-pack-manual');

        if (!$panel.length) {
            return;
        }

        hideLanguagePackLoader();

        var strings = getStringsForLocale(selectedLocale);
        var pack = packContext || settings.languagePack || {};
        var label = pack.label || ((strings.locale_labels && strings.locale_labels[pack.locale]) || pack.locale || selectedLocale);
        var needsNotice = !!pack.needs_notice;
        var justInstalled = !!pack.just_installed;

        if (justInstalled) {
            // Install success triggers an automatic page reload — no second Refresh button.
            $panel.prop('hidden', true);
            settings.languagePack = pack;
            return;
        }

        $('#breakdance-languages-wp-language-pack-message').text(
            needsNotice
                ? (strings.wp_language_pack_missing || '').replace('%s', label)
                : ''
        );

        if ($button.length) {
            $button
                .text(strings.wp_language_pack_install_button || '')
                .prop('hidden', !needsNotice || !pack.can_install)
                .prop('disabled', false);
        }

        if ($manual.length) {
            $manual
                .text(strings.wp_language_pack_manual_hint || '')
                .prop('hidden', !needsNotice || !!pack.can_install);
        }

        $panel.prop('hidden', !needsNotice);

        if (packContext) {
            settings.languagePack = packContext;
        }
    }

    function hideLanguagePackLoader(locale, finalPercent) {
        languagePackProgress.hide(locale || savedLocale, finalPercent);
    }

    function showLanguagePackLoader(locale) {
        var $button = $('#breakdance-languages-install-language-pack');

        if ($button.length) {
            $button.prop('hidden', true).prop('disabled', true);
        }

        $('#breakdance-languages-wp-language-pack-manual').prop('hidden', true);
        languagePackProgress.start(locale);
    }

    function installLanguagePack(locale) {
        return $.ajax({
            url: settings.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            timeout: 120000,
            data: {
                action: 'breakdance_languages_install_language_pack',
                nonce: settings.nonce,
                locale: locale
            }
        });
    }

    function showLoader(locale) {
        localeProgress.start(locale);

        updatePageCopy(locale);
        updateSelectLabels(locale);
        updateLicensePanel(locale);
        updateProfileNotice(locale);
        updateLanguagePackNotice(locale);
        $select.addClass('is-saving');
        if ($profileRefreshButton.length) {
            $profileRefreshButton.removeClass('is-emphasized');
        }
    }

    function hideLoader(locale, finalPercent) {
        localeProgress.hide(locale || savedLocale, finalPercent);
        $select.removeClass('is-saving');
    }

    function emphasizeProfileRefresh() {
        if (!$profileRefreshButton.length) {
            return;
        }

        $profileRefreshButton.addClass('is-emphasized');
    }

    function broadcastLocaleChange(payload) {
        var data = payload || {};

        applySettingsNonce(data);
        broadcastBreakdanceNonce(data.breakdanceNonce);

        try {
            localStorage.setItem(
                settings.storageKey,
                JSON.stringify(
                    $.extend(
                        {
                            ts: Date.now()
                        },
                        data
                    )
                )
            );
        } catch (error) {
            // Ignore storage failures and rely on page reload.
        }
    }

    function saveLocale(locale) {
        if (activeRequest && typeof activeRequest.abort === 'function') {
            activeRequest.abort();
        }

        showLoader(locale);
        setStatus('', '');

        activeRequest = $.ajax({
            url: settings.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'breakdance_languages_save_locale',
                nonce: settings.nonce,
                locale: locale
            }
        });

        return activeRequest;
    }

    function forceReloadPage() {
        var url = new URL(window.location.href);
        url.searchParams.set('bdl_reload', String(Date.now()));
        window.location.replace(url.toString());
    }

    function finishSaveSuccess(response, locale) {
        var strings = getStringsForLocale(locale);

        if (response && response.data) {
            applySettingsNonce(response.data);
        }

        if (!response || !response.success) {
            var errorMessage = response && response.data && response.data.message
                ? response.data.message
                : strings.error;
            setStatus(errorMessage, 'error');
            $select.val(savedLocale);
            updateSelectLabels(savedLocale);
            return;
        }

        if (response.data && response.data.strings) {
            strings = response.data.strings;
        }

        if (response.data && response.data.profileContext) {
            settings.profileContext = response.data.profileContext;
        }

        if (response.data && response.data.languagePack) {
            updateLanguagePackNotice(locale, response.data.languagePack);
        }

        savedLocale = $select.val();
        updatePageCopy(savedLocale);
        updateSelectLabels(savedLocale);
        updateLicensePanel(savedLocale);
        updateProfileNotice(savedLocale);

        var statusMessage = response.data.message || strings.success || strings.ready;

        if (response.data.languagePack && response.data.languagePack.just_installed) {
            statusMessage = (strings.wp_language_pack_installed || statusMessage).replace(
                '%s',
                response.data.languagePack.label || locale
            );
        }

        setStatus(statusMessage, 'success');
        broadcastLocaleChange(response.data || {});
        emphasizeProfileRefresh();
    }

    function handleSaveSuccess(response, locale) {
        if (!response || !response.success) {
            hideLoader(locale);
            finishSaveSuccess(response, locale);
            return;
        }

        localeProgress.set(100, locale);

        window.setTimeout(function () {
            hideLoader(locale);
            finishSaveSuccess(response, locale);
        }, 350);
    }

    function handleSaveError(xhr, locale) {
        hideLoader(locale);

        setStatus(getAjaxErrorMessage(xhr, locale), 'error');
        $select.val(savedLocale);
        updateSelectLabels(savedLocale);
    }

    if ($profileRefreshButton.length) {
        $profileRefreshButton.on('click', function (event) {
            event.preventDefault();
            broadcastLocaleChange({ forceReload: true });
            forceReloadPage();
        });
    }

    $('#breakdance-languages-install-language-pack').on('click', function (event) {
        event.preventDefault();

        var locale = $select.val();

        if (!locale || locale === settings.autoLocale) {
            return;
        }

        showLanguagePackLoader(locale);
        setStatus('', '');

        installLanguagePack(locale)
            .done(function (response) {
                languagePackProgress.set(100, locale);

                window.setTimeout(function () {
                    hideLanguagePackLoader(locale);

                    var strings = getStringsForLocale(locale);
                    var message = strings.wp_language_pack_installed || '';
                    var pack = response && response.data && response.data.languagePack
                        ? response.data.languagePack
                        : null;

                    if (response && response.data) {
                        applySettingsNonce(response.data);
                    }

                    if (!response || !response.success) {
                        var errorMessage = response && response.data && response.data.message
                            ? response.data.message
                            : (strings.wp_language_pack_install_error || strings.error);

                        if (pack) {
                            updateLanguagePackNotice(locale, pack);
                        } else {
                            updateLanguagePackNotice(locale);
                        }

                        setStatus(errorMessage, 'error');
                        return;
                    }

                    if (pack) {
                        pack.just_installed = true;
                        updateLanguagePackNotice(locale, pack);
                        message = (strings.wp_language_pack_installed || message).replace(
                            '%s',
                            pack.label || locale
                        );
                    }

                    if (response.data && response.data.message) {
                        message = response.data.message;
                    }

                    setStatus(message, 'success');

                    // Same effect as the selector "Refresh" button — reload once pack is in.
                    window.setTimeout(function () {
                        broadcastLocaleChange({ forceReload: true, languagePackInstalled: true });
                        forceReloadPage();
                    }, 700);
                }, 350);
            })
            .fail(function (xhr) {
                hideLanguagePackLoader(locale);

                var strings = getStringsForLocale(locale);
                var message = getAjaxErrorMessage(xhr, locale);

                if (message === strings.error && strings.wp_language_pack_install_error) {
                    message = strings.wp_language_pack_install_error;
                }

                if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.languagePack) {
                    updateLanguagePackNotice(locale, xhr.responseJSON.data.languagePack);
                } else {
                    updateLanguagePackNotice(locale);
                }

                setStatus(message, 'error');
            });
    });

    if ($select.length) {
        updatePageCopy(savedLocale);
        updateSelectLabels(savedLocale);
        updateLicensePanel(savedLocale);
        updateProfileNotice(savedLocale);
        updateLanguagePackNotice(savedLocale);
    }

    if (!settings.isLicensed || !$select.length) {
        return;
    }

    $select.on('change', function () {
        var locale = $select.val();

        if (locale === savedLocale) {
            return;
        }

        // Preview copy (eyebrow, tips, upsell) in the selected language immediately.
        updatePageCopy(locale);
        updateSelectLabels(locale);
        updateLicensePanel(locale);
        updateProfileNotice(locale);

        saveLocale(locale)
            .done(function (response) {
                handleSaveSuccess(response, locale);
            })
            .fail(function (xhr) {
                handleSaveError(xhr, locale);
            })
            .always(function () {
                activeRequest = null;
            });
    });
}(jQuery));
