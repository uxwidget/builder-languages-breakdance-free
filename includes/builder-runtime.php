<?php
/**
 * Builder Languages for Breakdance — Builder runtime.
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
 * Whether translations should load for the current builder-facing request.
 */
function breakdance_languages_is_builder_runtime_request(): bool
{
    if (!breakdance_languages_is_breakdance_active()) {
        return false;
    }

    if (breakdance_languages_is_breakdance_ajax_request()) {
        return true;
    }

    return breakdance_languages_freemius_is_builder_request();
}

/**
 * Load Breakdance runtime textdomains once per request.
 */
function breakdance_languages_ensure_builder_runtime_textdomains(): void
{
    static $loaded = false;

    if ($loaded || !breakdance_languages_is_builder_runtime_request()) {
        return;
    }

    if (!breakdance_languages_can_apply_translations()) {
        return;
    }

    $loaded = true;
    breakdance_languages_load_breakdance_textdomains();
}

add_action('plugins_loaded', 'breakdance_languages_ensure_builder_runtime_textdomains', 5);
add_action('init', 'breakdance_languages_ensure_builder_runtime_textdomains', 0);
add_action('breakdance_loaded', 'breakdance_languages_ensure_builder_runtime_textdomains', 0);

/**
 * Hardcoded section-tab labels when gettext is unavailable in builder AJAX.
 *
 * @return array<string, string>
 */
function breakdance_languages_section_label_fallbacks(?string $locale = null): array
{
    $locale = $locale ?: breakdance_languages_resolve_locale();

    $maps = [
        'pt_BR' => [
            'Typography' => 'Tipografia',
            'Size' => 'Tamanho',
            'Spacing' => 'Espaçamento',
            'Content' => 'Conteúdo',
            'Design' => 'Design',
            'Settings' => 'Configurações',
            'Background' => 'Fundo',
            'Layout' => 'Disposição',
            'Borders' => 'Bordas',
            'Buttons' => 'Botões',
            'Effects' => 'Efeitos',
            'Overlay' => 'Sobreposição',
            'Padding' => 'Preenchimento',
            'Dividers' => 'Divisores',
            'Text Colors' => 'Cores do texto',
            'Primary' => 'Primário',
            'Secondary' => 'Secundário',
            'Filter' => 'Filtro',
            'Image' => 'Imagem',
            'Video' => 'Vídeo',
            'Gradient' => 'Gradiente',
            'Text' => 'Texto',
        ],
        'pt_PT' => [
            'Typography' => 'Tipografia',
            'Size' => 'Tamanho',
            'Spacing' => 'Espaçamento',
            'Content' => 'Conteúdo',
            'Design' => 'Design',
            'Settings' => 'Definições',
            'Background' => 'Fundo',
            'Layout' => 'Disposição',
            'Borders' => 'Bordas',
            'Buttons' => 'Botões',
            'Effects' => 'Efeitos',
            'Overlay' => 'Sobreposição',
            'Padding' => 'Preenchimento',
            'Dividers' => 'Divisores',
            'Text Colors' => 'Cores do texto',
            'Primary' => 'Primário',
            'Secondary' => 'Secundário',
            'Filter' => 'Filtro',
            'Image' => 'Imagem',
            'Video' => 'Vídeo',
            'Gradient' => 'Gradiente',
            'Text' => 'Texto',
        ],
    ];

    if ($locale === null || !isset($maps[$locale])) {
        return [];
    }

    return $maps[$locale];
}

/**
 * Translate a Breakdance Elements string, trying preset-section context as fallback.
 */
function breakdance_languages_translate_elements_string(
    string $string,
    string $context,
    string $domain = 'breakdance-elements'
): string {
    if ($string === '') {
        return $string;
    }

    $translated = _x($string, $context, $domain);

    if ($translated !== $string) {
        return $translated;
    }

    if ($context === 'Control label') {
        $preset = _x($string, 'Preset section label', $domain);

        if ($preset !== $string) {
            return $preset;
        }
    }

    return $string;
}

/**
 * Translate a control label, preferring preset-section context for section tabs.
 *
 * @param array<string, mixed> $control
 */
function breakdance_languages_translate_control_label(array $control, string $domain, ?string $locale = null): string
{
    $label = $control['label'] ?? '';

    if (!is_string($label) || $label === '') {
        return is_string($label) ? $label : '';
    }

    $type = $control['options']['type'] ?? '';
    $section_type = $control['options']['sectionOptions']['type'] ?? '';
    $is_preset_section = isset($control['options']['sectionOptions']['preset']);
    $is_section_like = $type === 'section'
        || $section_type === 'popout'
        || $section_type === 'modal'
        || $is_preset_section;

    if ($is_section_like) {
        $preset_label = _x($label, 'Preset section label', $domain);

        if ($preset_label !== $label) {
            return $preset_label;
        }
    }

    $translated = breakdance_languages_translate_elements_string($label, 'Control label', $domain);

    if ($translated !== $label) {
        return $translated;
    }

    $fallbacks = breakdance_languages_section_label_fallbacks($locale);

    return $fallbacks[$label] ?? $label;
}

/**
 * Recursively translate element control labels for the active locale.
 *
 * @param array<array-key, mixed> $controls
 * @return array<array-key, mixed>
 */
function breakdance_languages_translate_controls_recursive(array $controls, string $domain, ?string $locale = null): array
{
    foreach ($controls as $index => $control) {
        if (!is_array($control)) {
            continue;
        }

        if (isset($control['label']) && is_string($control['label']) && $control['label'] !== '') {
            $control['label'] = breakdance_languages_translate_control_label($control, $domain, $locale);
        }

        if (
            isset($control['options']['items'])
            && is_array($control['options']['items'])
        ) {
            foreach ($control['options']['items'] as $item_index => $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (isset($item['label']) && is_string($item['label']) && $item['label'] !== '' && $item['label'] !== 'Label') {
                    $item['label'] = breakdance_languages_translate_elements_string(
                        $item['label'],
                        'Item label',
                        $domain
                    );
                }

                if (isset($item['text']) && is_string($item['text']) && $item['text'] !== '') {
                    $item['text'] = breakdance_languages_translate_elements_string(
                        $item['text'],
                        'Item text',
                        $domain
                    );
                }

                $control['options']['items'][$item_index] = $item;
            }
        }

        if (
            isset($control['options']['placeholder'])
            && is_string($control['options']['placeholder'])
            && $control['options']['placeholder'] !== ''
        ) {
            $control['options']['placeholder'] = breakdance_languages_translate_elements_string(
                $control['options']['placeholder'],
                'Placeholder',
                $domain
            );
        }

        if (isset($control['children']) && is_array($control['children'])) {
            $control['children'] = breakdance_languages_translate_controls_recursive($control['children'], $domain, $locale);
        }

        $controls[$index] = $control;
    }

    return $controls;
}

/**
 * Ensure element control labels translate even when Breakdance skips English locale checks.
 *
 * @param array<string, mixed> $controls
 * @param object               $element
 * @return array<string, mixed>
 */
function breakdance_languages_translate_element_controls(array $controls, $element): array
{
    unset($element);

    if (!breakdance_languages_can_apply_translations() || !breakdance_languages_is_builder_runtime_request()) {
        return $controls;
    }

    $locale = breakdance_languages_resolve_locale();

    if ($locale === null || in_array($locale, ['en_US', 'en_GB', 'en'], true)) {
        return $controls;
    }

    breakdance_languages_ensure_builder_runtime_textdomains();
    breakdance_languages_load_breakdance_textdomains($locale);

    $domain = 'breakdance-elements';

    foreach (['contentSections', 'designSections', 'settingsSections'] as $section_key) {
        if (!isset($controls[$section_key]) || !is_array($controls[$section_key])) {
            continue;
        }

        $controls[$section_key] = breakdance_languages_translate_controls_recursive(
            $controls[$section_key],
            $domain,
            $locale
        );
    }

    return $controls;
}

add_filter('breakdance_element_controls', 'breakdance_languages_translate_element_controls', 99, 2);

/**
 * Translate element names when Breakdance skips gettext for English-looking locales.
 */
add_filter('breakdance_element_name', 'breakdance_languages_translate_element_name', 5, 2);

/**
 * @param string $name
 * @param object $element
 */
function breakdance_languages_translate_element_name(string $name, $element): string
{
    unset($element);

    if (!breakdance_languages_can_apply_translations() || !breakdance_languages_is_builder_runtime_request()) {
        return $name;
    }

    $locale = breakdance_languages_resolve_locale();

    if ($locale === null || in_array($locale, ['en_US', 'en_GB', 'en'], true)) {
        return $name;
    }

    breakdance_languages_ensure_builder_runtime_textdomains();

    if (!is_textdomain_loaded('breakdance-elements')) {
        return $name;
    }

    $translated = breakdance_languages_translate_elements_string($name, 'Element name', 'breakdance-elements');

    return $translated !== '' ? $translated : $name;
}
