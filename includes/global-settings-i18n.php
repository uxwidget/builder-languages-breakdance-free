<?php
/**
 * Builder Languages for Breakdance — Global Settings i18n.
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

add_filter(
    'breakdance_global_settings_enable_default_control_sections',
    'breakdance_languages_maybe_disable_default_global_settings',
    999
);
add_filter(
    'breakdance_global_settings_control_sections_append',
    'breakdance_languages_append_translated_global_settings',
    1000
);
add_filter(
    'breakdance_languages_editor_label_dictionary',
    'breakdance_languages_merge_form_ui_editor_labels',
    15,
    2
);

/**
 * Whether Global Settings controls should be re-injected with translations.
 */
function breakdance_languages_should_translate_global_settings(): bool
{
    if (!breakdance_languages_is_breakdance_active()) {
        return false;
    }

    if (!breakdance_languages_is_builder_runtime_request() && !breakdance_languages_is_builder_page()) {
        return false;
    }

    if (!breakdance_languages_is_licensed() && !breakdance_languages_can_apply_translations()) {
        return breakdance_languages_is_local_dev_site();
    }

    $locale = breakdance_languages_resolve_locale();

    return $locale !== null && !in_array($locale, ['en_US', 'en_GB', 'en'], true);
}

/**
 * @param bool $enable
 */
function breakdance_languages_maybe_disable_default_global_settings($enable): bool
{
    if (!breakdance_languages_should_translate_global_settings()) {
        return (bool) $enable;
    }

    if (!function_exists('\Breakdance\GlobalSettings\COLORS_SECTION')) {
        return (bool) $enable;
    }

    return false;
}

/**
 * Rebuild default Global Settings sections with translated labels/items.
 *
 * @param array<int, mixed> $additional
 * @return array<int, mixed>
 */
function breakdance_languages_append_translated_global_settings(array $additional): array
{
    if (!breakdance_languages_should_translate_global_settings()) {
        return $additional;
    }

    static $injected = false;

    if ($injected) {
        return $additional;
    }

    $injected = true;

    breakdance_languages_ensure_builder_runtime_textdomains();
    breakdance_languages_load_breakdance_textdomains();

    $sections = breakdance_languages_get_default_global_settings_sections();
    $sections = breakdance_languages_translate_global_settings_sections($sections);

    return array_merge($sections, $additional);
}

/**
 * @return array<int, mixed>
 */
function breakdance_languages_get_default_global_settings_sections(): array
{
    if (!function_exists('\Breakdance\GlobalSettings\COLORS_SECTION')) {
        return [];
    }

    $sections = [
        \Breakdance\GlobalSettings\COLORS_SECTION(),
        \Breakdance\GlobalSettings\BUTTONS_SECTION(),
        \Breakdance\GlobalSettings\TYPOGRAPHY_SECTION(),
        \Breakdance\GlobalSettings\FORMS_SECTION(),
        \Breakdance\GlobalSettings\CONTAINERS_SECTION(),
        \Breakdance\GlobalSettings\ADVANCED_SECTION(),
        \Breakdance\GlobalSettings\OTHER_SECTION(),
    ];

    if (
        function_exists('\Breakdance\WooCommerce\Settings\isWooIntegrationEnabled')
        && function_exists('\Breakdance\GlobalSettings\WooCommerce\WOO_SECTION')
        && \Breakdance\WooCommerce\Settings\isWooIntegrationEnabled()
    ) {
        $sections[] = \Breakdance\GlobalSettings\WooCommerce\WOO_SECTION();
    }

    return $sections;
}

/**
 * @param array<int, mixed> $sections
 * @return array<int, mixed>
 */
function breakdance_languages_translate_global_settings_sections(array $sections): array
{
    $locale = breakdance_languages_resolve_locale();

    foreach ($sections as $index => $section) {
        if (!is_array($section)) {
            continue;
        }

        $sections[$index] = breakdance_languages_translate_global_settings_control_tree($section, $locale);
    }

    return $sections;
}

/**
 * @param array<string, mixed> $control
 * @return array<string, mixed>
 */
function breakdance_languages_translate_global_settings_control_tree(array $control, ?string $locale): array
{
    if (isset($control['label']) && is_string($control['label']) && $control['label'] !== '') {
        $control['label'] = breakdance_languages_translate_global_settings_label(
            $control['label'],
            $control,
            $locale
        );
    }

    if (isset($control['options']['items']) && is_array($control['options']['items'])) {
        foreach ($control['options']['items'] as $item_index => $item) {
            if (!is_array($item)) {
                continue;
            }

            if (isset($item['label']) && is_string($item['label']) && $item['label'] !== '' && $item['label'] !== 'Label') {
                $item['label'] = breakdance_languages_translate_elements_string(
                    $item['label'],
                    'Item label',
                    'breakdance-elements'
                );
            }

            if (isset($item['text']) && is_string($item['text']) && $item['text'] !== '') {
                $item['text'] = breakdance_languages_translate_elements_string(
                    $item['text'],
                    'Item text',
                    'breakdance-elements'
                );
            }

            $control['options']['items'][$item_index] = $item;
        }
    }

    if (isset($control['options']['placeholder']) && is_string($control['options']['placeholder']) && $control['options']['placeholder'] !== '') {
        $control['options']['placeholder'] = breakdance_languages_translate_elements_string(
            $control['options']['placeholder'],
            'Placeholder',
            'breakdance-elements'
        );
    }

    if (isset($control['children']) && is_array($control['children'])) {
        foreach ($control['children'] as $child_index => $child) {
            if (!is_array($child)) {
                continue;
            }

            $control['children'][$child_index] = breakdance_languages_translate_global_settings_control_tree(
                $child,
                $locale
            );
        }
    }

    return $control;
}

/**
 * @param array<string, mixed> $control
 */
function breakdance_languages_translate_global_settings_label(
    string $label,
    array $control,
    ?string $locale
): string {
    $type = $control['options']['type'] ?? '';
    $section_type = $control['options']['sectionOptions']['type'] ?? '';
    $is_preset_section = isset($control['options']['sectionOptions']['preset']);
    $is_section_like = $type === 'section'
        || $section_type === 'popout'
        || $section_type === 'modal'
        || $section_type === 'accordion'
        || $is_preset_section;

    if ($is_section_like) {
        $preset = _x($label, 'Preset section label', 'breakdance-elements');

        if ($preset !== $label) {
            return $preset;
        }
    }

    $elements = breakdance_languages_translate_elements_string($label, 'Control label', 'breakdance-elements');

    if ($elements !== $label) {
        return $elements;
    }

    $builder = __($label, 'breakdance');

    if ($builder !== $label) {
        return $builder;
    }

    $dictionary = breakdance_languages_form_ui_maps()[$locale ?? ''] ?? [];

    return $dictionary[$label] ?? $label;
}

/**
 * Runtime fallback labels for Global Settings / Forms UI.
 *
 * @return array<string, array<string, string>>
 */
function breakdance_languages_form_ui_maps(): array
{
    return [
        'pt_BR' => [
            'Corners' => 'Cantos',
            'Square' => 'Quadrado',
            'Round' => 'Redondo',
            'Custom' => 'Personalizado',
            'Custom...' => 'Personalizado...',
            'Outline' => 'Contorno',
            'Small' => 'Pequeno',
            'Default' => 'Padrão',
            'Large' => 'Grande',
            'Position' => 'Posição',
            'Before' => 'Antes',
            'After' => 'Depois',
            'Space Before' => 'Espaço antes',
            'Space After' => 'Espaço depois',
            'Fields' => 'Campos',
            'Form' => 'Formulário',
            'Messages' => 'Mensagens',
            'Submit Button' => 'Botão enviar',
            'Other' => 'Outro',
            'Stepper' => 'Passo a passo',
            'Disable' => 'Desativar',
            'Width' => 'Largura',
            'Alignment' => 'Alinhamento',
            'Step' => 'Etapa',
            'Divider' => 'Divisor',
            'Previous Button' => 'Botão anterior',
            'Next Button' => 'Próximo botão',
            'Left' => 'Esquerda',
            'Center' => 'Centro',
            'Right' => 'Direita',
            'Full Width' => 'Largura total',
            'Styles' => 'Estilos',
            'Space Above' => 'Espaço acima',
            'Error' => 'Erro',
            'Success' => 'Sucesso',
            'Required' => 'Obrigatório',
            'Color' => 'Cor',
            'Nudge X' => 'Horizontal X',
            'Nudge Y' => 'Vertical Y',
            'Empurrar X' => 'Horizontal X',
            'Cutucar Y' => 'Vertical Y',
            'Radio & Checkbox' => 'Radio e checkbox',
            'File Input' => 'Campo de arquivo',
            'Responsive' => 'Responsivo',
            'Drag & Drop' => 'Arrastar e soltar',
            'Border Color' => 'Cor da borda',
            'Icon Color' => 'Cor do ícone',
            'Active' => 'Ativo',
            'Advanced' => 'Avançado',
        ],
        'pt_PT' => [
            'Corners' => 'Cantos',
            'Square' => 'Quadrado',
            'Round' => 'Redondo',
            'Custom' => 'Personalizado',
            'Custom...' => 'Personalizado...',
            'Outline' => 'Contorno',
            'Small' => 'Pequeno',
            'Default' => 'Predefinição',
            'Large' => 'Grande',
            'Position' => 'Posição',
            'Before' => 'Antes',
            'After' => 'Depois',
            'Space Before' => 'Espaço antes',
            'Space After' => 'Espaço depois',
            'Fields' => 'Campos',
            'Messages' => 'Mensagens',
            'Submit Button' => 'Botão enviar',
            'Other' => 'Outro',
            'Stepper' => 'Passo a passo',
            'Disable' => 'Desativar',
            'Width' => 'Largura',
            'Alignment' => 'Alinhamento',
            'Step' => 'Etapa',
            'Divider' => 'Divisor',
            'Previous Button' => 'Botão anterior',
            'Next Button' => 'Botão seguinte',
            'Left' => 'Esquerda',
            'Center' => 'Centro',
            'Right' => 'Direita',
            'Full Width' => 'Largura total',
            'Styles' => 'Estilos',
            'Space Above' => 'Espaço acima',
            'Error' => 'Erro',
            'Success' => 'Sucesso',
            'Required' => 'Obrigatório',
            'Color' => 'Cor',
            'Nudge X' => 'Deslocamento X',
            'Nudge Y' => 'Deslocamento Y',
            'Radio & Checkbox' => 'Rádio e caixa de seleção',
            'File Input' => 'Campo de ficheiro',
            'Responsive' => 'Responsivo',
            'Drag & Drop' => 'Arrastar e largar',
            'Border Color' => 'Cor da borda',
            'Icon Color' => 'Cor do ícone',
            'Active' => 'Ativo',
            'Advanced' => 'Avançado',
        ],
        'ja_JP' => [
            'Corners' => '角',
            'Square' => '四角',
            'Round' => '丸',
            'Custom' => 'カスタム',
            'Custom...' => 'カスタム...',
            'Outline' => 'アウトライン',
            'Small' => '小',
            'Default' => 'デフォルト',
            'Large' => '大',
            'Position' => '位置',
            'Before' => '前',
            'After' => '後',
            'Space Before' => '前の余白',
            'Space After' => '後の余白',
            'Fields' => 'フィールド',
            'Messages' => 'メッセージ',
            'Submit Button' => '送信ボタン',
            'Other' => 'その他',
            'Stepper' => 'ステッパー',
            'Disable' => '無効',
            'Width' => '幅',
            'Alignment' => '配置',
            'Step' => 'ステップ',
            'Divider' => '区切り',
            'Previous Button' => '前へボタン',
            'Next Button' => '次へボタン',
            'Left' => '左',
            'Center' => '中央',
            'Right' => '右',
            'Full Width' => '全幅',
            'Styles' => 'スタイル',
            'Space Above' => '上の余白',
            'Error' => 'エラー',
            'Success' => '成功',
            'Required' => '必須',
            'Color' => '色',
            'Nudge X' => 'X方向の調整',
            'Nudge Y' => 'Y方向の調整',
            'Radio & Checkbox' => 'ラジオとチェックボックス',
            'File Input' => 'ファイル入力',
            'Responsive' => 'レスポンシブ',
            'Drag & Drop' => 'ドラッグ＆ドロップ',
            'Border Color' => '枠線の色',
            'Icon Color' => 'アイコンの色',
            'Active' => 'アクティブ',
            'Advanced' => '高度',
        ],
    ];
}

/**
 * @return array<string, string>
 */
function breakdance_languages_get_form_ui_dictionary(?string $locale = null): array
{
    if (!breakdance_languages_should_inject_editor_overrides()) {
        return [];
    }

    $locale = $locale ?: breakdance_languages_resolve_editor_locale();

    if ($locale === null) {
        return [];
    }

    return breakdance_languages_form_ui_maps()[$locale] ?? [];
}

/**
 * @param array<string, string> $dictionary
 * @return array<string, string>
 */
function breakdance_languages_merge_form_ui_editor_labels(array $dictionary, ?string $locale): array
{
    $form = breakdance_languages_get_form_ui_dictionary($locale);

    if ($form === []) {
        return $dictionary;
    }

    return array_merge($dictionary, $form);
}
