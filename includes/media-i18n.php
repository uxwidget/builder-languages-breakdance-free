<?php
/**
 * Media chooser iframe and media-size labels that bypass wp.i18n.
 *
 * @package Breakdance_Languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', 'breakdance_languages_enqueue_media_chooser_i18n', 20);
add_filter('breakdance_languages_editor_label_dictionary', 'breakdance_languages_merge_media_editor_labels', 10, 2);

/**
 * Hardcoded media / dynamic-data type labels per locale.
 *
 * @return array<string, array<string, string>>
 */
function breakdance_languages_media_ui_maps(): array
{
    return [
        'pt_BR' => [
            'Choose' => 'Escolher',
            'Upload Media' => 'Enviar mídia',
            'All' => 'Todos',
            'String' => 'Texto',
            'Image url' => 'URL da imagem',
            'Image Url' => 'URL da imagem',
            'Query' => 'Consulta',
            'Url' => 'URL',
            'Full' => 'Completo',
            'Thumbnail' => 'Miniatura',
            'Medium' => 'Médio',
            'Medium Large' => 'Médio grande',
            'Large' => 'Grande',
            'cropped' => 'recortado',
            'constrained proportions' => 'proporções restritas',
        ],
        'pt_PT' => [
            'Choose' => 'Escolher',
            'Upload Media' => 'Carregar multimédia',
            'All' => 'Todos',
            'String' => 'Texto',
            'Image url' => 'URL da imagem',
            'Image Url' => 'URL da imagem',
            'Query' => 'Consulta',
            'Url' => 'URL',
            'Full' => 'Completo',
            'Thumbnail' => 'Miniatura',
            'Medium' => 'Médio',
            'Medium Large' => 'Médio grande',
            'Large' => 'Grande',
            'cropped' => 'recortado',
            'constrained proportions' => 'proporções restritas',
        ],
        'ja_JP' => [
            'Choose' => '選択',
            'Upload Media' => 'メディアをアップロード',
            'All' => 'すべて',
            'String' => '文字列',
            'Image url' => '画像URL',
            'Image Url' => '画像URL',
            'Query' => 'クエリ',
            'Url' => 'URL',
            'Full' => 'フル',
            'Thumbnail' => 'サムネイル',
            'Medium' => '中',
            'Medium Large' => '中サイズ',
            'Large' => '大',
            'cropped' => '切り抜き',
            'constrained proportions' => '比率を維持',
        ],
    ];
}

/**
 * @return array<string, string>
 */
function breakdance_languages_get_media_ui_dictionary(?string $locale = null): array
{
    if (!breakdance_languages_should_inject_editor_overrides()) {
        return [];
    }

    $locale = $locale ?: breakdance_languages_resolve_editor_locale();

    if ($locale === null) {
        return [];
    }

    $maps = breakdance_languages_media_ui_maps();

    return $maps[$locale] ?? [];
}

/**
 * @param array<string, string> $dictionary
 * @return array<string, string>
 */
function breakdance_languages_merge_media_editor_labels(array $dictionary, ?string $locale): array
{
    $media = breakdance_languages_get_media_ui_dictionary($locale);

    if ($media === []) {
        return $dictionary;
    }

    return array_merge($dictionary, $media);
}

/**
 * Resolve locale for the media chooser iframe.
 */
function breakdance_languages_resolve_media_chooser_locale(): ?string
{
    $supported = array_keys(breakdance_languages_media_ui_maps());
    $preference = breakdance_languages_get_user_builder_locale();

    if ($preference !== BREAKDANCE_LANGUAGES_AUTO_LOCALE) {
        $matched = breakdance_languages_match_supported_locale($preference);

        if ($matched !== null && in_array($matched, $supported, true)) {
            return $matched;
        }
    }

    if (!breakdance_languages_can_apply_translations()) {
        return null;
    }

    $locale = breakdance_languages_resolve_locale();

    if ($locale !== null && in_array($locale, $supported, true)) {
        return $locale;
    }

    return null;
}

/**
 * Inject translations into the Breakdance media chooser iframe.
 */
function breakdance_languages_enqueue_media_chooser_i18n(): void
{
    if (!isset($_GET['breakdance_wpuiforbuilder_media']) || !$_GET['breakdance_wpuiforbuilder_media']) {
        return;
    }

    if (!breakdance_languages_is_licensed() && !breakdance_languages_can_apply_translations()) {
        if (!breakdance_languages_is_local_dev_site()) {
            return;
        }
    }

    $locale = breakdance_languages_resolve_media_chooser_locale();

    if ($locale === null) {
        return;
    }

    $maps = breakdance_languages_media_ui_maps();
    $labels = $maps[$locale] ?? [];

    if ($labels === []) {
        return;
    }

    if (!wp_script_is('breakdance-media-control', 'enqueued')) {
        return;
    }

    wp_localize_script('breakdance-media-control', 'breakdanceLanguagesMedia', [
        'choose' => $labels['Choose'] ?? 'Choose',
        'uploadMedia' => $labels['Upload Media'] ?? 'Upload Media',
    ]);

    $inline = <<<'JS'
(function ($) {
    var i18n = window.breakdanceLanguagesMedia || {};

    function patchMediaFrame() {
        if (i18n.uploadMedia) {
            document.querySelectorAll('.media-frame-title h1, .media-frame-menu-heading').forEach(function (node) {
                if (node.textContent.trim() === 'Upload Media') {
                    node.textContent = i18n.uploadMedia;
                }
            });
        }

        if (i18n.choose) {
            document.querySelectorAll(
                '.media-toolbar-primary .button.media-button-select, .media-toolbar-primary .button'
            ).forEach(function (node) {
                if (node.textContent.trim() === 'Choose') {
                    node.textContent = i18n.choose;
                }
            });
        }
    }

    $(document).ready(function () {
        patchMediaFrame();

        var observer = new MutationObserver(function () {
            patchMediaFrame();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true
        });
    });
})(jQuery);
JS;

    wp_add_inline_script('breakdance-media-control', $inline, 'after');
}
