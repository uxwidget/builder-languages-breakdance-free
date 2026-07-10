<?php
/**
 * Hardcoded Breakdance admin strings and settings tab output overrides.
 *
 * Some Breakdance admin screens ship English text without gettext wrappers.
 *
 * @package Breakdance_Languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'breakdance_languages_register_settings_tab_output_filters', 11);
add_action('init', 'breakdance_languages_fixup_settings_tab_labels', 11);

/**
 * Register output buffering on each Breakdance settings tab.
 */
function breakdance_languages_register_settings_tab_output_filters(): void
{
    if (!is_admin() || breakdance_languages_should_skip_admin_bootstrap()) {
        return;
    }

    if (!breakdance_languages_is_breakdance_active() || !class_exists('\Breakdance\Admin\SettingsPage\SettingsPageController')) {
        return;
    }

    $controller = \Breakdance\Admin\SettingsPage\SettingsPageController::getInstance();

    foreach ($controller->tabs as $tab) {
        if (!isset($tab['slug']) || !is_string($tab['slug']) || $tab['slug'] === '') {
            continue;
        }

        $hook = 'breakdance_admin_settings_page_tabs_' . $tab['slug'] . '_tab';

        add_action($hook, static function (): void {
            ob_start();
        }, 0);

        add_action($hook, static function (): void {
            $html = ob_get_clean();

            if (!is_string($html)) {
                return;
            }

            echo breakdance_languages_apply_admin_html_overrides($html);
        }, PHP_INT_MAX);
    }
}

/**
 * Translate tab labels that Breakdance registers without gettext.
 */
function breakdance_languages_fixup_settings_tab_labels(): void
{
    if (!is_admin() || breakdance_languages_should_skip_admin_bootstrap()) {
        return;
    }

    if (!breakdance_languages_is_breakdance_active() || !class_exists('\Breakdance\Admin\SettingsPage\SettingsPageController')) {
        return;
    }

    $replacements = breakdance_languages_get_admin_hardcoded_replacements();

    if ($replacements === []) {
        return;
    }

    $controller = \Breakdance\Admin\SettingsPage\SettingsPageController::getInstance();

    foreach ($controller->tabs as &$tab) {
        if (!isset($tab['name'], $tab['slug']) || !is_string($tab['name'])) {
            continue;
        }

        if ($tab['slug'] === 'elements' && isset($replacements['Elements'])) {
            $tab['name'] = $replacements['Elements'];
        }
    }

    unset($tab);
}

/**
 * Replace known hardcoded English admin strings in rendered HTML.
 */
function breakdance_languages_apply_admin_html_overrides(string $html): string
{
    $replacements = breakdance_languages_get_admin_hardcoded_replacements();

    if ($replacements === []) {
        return $html;
    }

    uksort(
        $replacements,
        static function (string $left, string $right): int {
            return strlen($right) <=> strlen($left);
        }
    );

    return strtr($html, $replacements);
}

/**
 * @return array<string, string>
 */
function breakdance_languages_get_admin_hardcoded_replacements(): array
{
    if (!breakdance_languages_should_apply_builder_locale()) {
        return [];
    }

    if (!breakdance_languages_is_licensed()) {
        return [];
    }

    $locale = breakdance_languages_resolve_locale();

    if ($locale === null || in_array($locale, ['en_US', 'en_GB'], true)) {
        return [];
    }

    $maps = breakdance_languages_admin_hardcoded_string_maps();

    return $maps[$locale] ?? [];
}

/**
 * @return array<string, array<string, string>>
 */
function breakdance_languages_admin_hardcoded_string_maps(): array
{
    return [
        'pt_BR' => [
            'Select elements to hide from the Add panel in the builder. Hidden elements will not appear in the Add panel, but existing instances in designs remain functional.' => 'Selecione os elementos para ocultar do painel Adicionar no builder. Elementos ocultos não aparecerão no painel Adicionar, mas instâncias existentes nos designs continuarão funcionando.',
            'Accessibility: Skip Link' => 'Acessibilidade: link de pular',
            'Enables a skip link and wraps the content in a <code>&lt;main&gt;</code> tag to allow users to skip the header and go straight to the main content. This is useful for screen readers and keyboard navigation.' => 'Ativa um link de pular e envolve o conteúdo em uma tag <code>&lt;main&gt;</code> para permitir que os usuários pulem o cabeçalho e vão direto ao conteúdo principal. Útil para leitores de tela e navegação por teclado.',
            'Skip Link Text' => 'Texto do link de pular',
            'Filter elements…' => 'Filtrar elementos…',
            'Hidden Elements' => 'Elementos ocultos',
            'Select All' => 'Selecionar tudo',
            'Clear All' => 'Limpar tudo',
            '<h2>Elements</h2>' => '<h2>Elementos</h2>',
            'id="enable_skip_link"> Enable' => 'id="enable_skip_link"> Ativar',
            'value="Save Changes"' => 'value="Salvar alterações"',
        ],
        'pt_PT' => [
            'Select elements to hide from the Add panel in the builder. Hidden elements will not appear in the Add panel, but existing instances in designs remain functional.' => 'Selecione os elementos para ocultar do painel Adicionar no builder. Os elementos ocultos não aparecerão no painel Adicionar, mas as instâncias existentes nos designs continuarão funcionais.',
            'Accessibility: Skip Link' => 'Acessibilidade: ligação de salto',
            'Enables a skip link and wraps the content in a <code>&lt;main&gt;</code> tag to allow users to skip the header and go straight to the main content. This is useful for screen readers and keyboard navigation.' => 'Ativa uma ligação de salto e envolve o conteúdo numa etiqueta <code>&lt;main&gt;</code> para permitir que os utilizadores saltem o cabeçalho e vão diretamente ao conteúdo principal. Útil para leitores de ecrã e navegação por teclado.',
            'Skip Link Text' => 'Texto da ligação de salto',
            'Filter elements…' => 'Filtrar elementos…',
            'Hidden Elements' => 'Elementos ocultos',
            'Select All' => 'Selecionar tudo',
            'Clear All' => 'Limpar tudo',
            '<h2>Elements</h2>' => '<h2>Elementos</h2>',
            'id="enable_skip_link"> Enable' => 'id="enable_skip_link"> Ativar',
            'value="Save Changes"' => 'value="Guardar alterações"',
        ],
        'ja_JP' => [
            'Select elements to hide from the Add panel in the builder. Hidden elements will not appear in the Add panel, but existing instances in designs remain functional.' => 'ビルダーの追加パネルから非表示にする要素を選択します。非表示の要素は追加パネルに表示されませんが、デザイン内の既存インスタンスは引き続き機能します。',
            'Accessibility: Skip Link' => 'アクセシビリティ：スキップリンク',
            'Enables a skip link and wraps the content in a <code>&lt;main&gt;</code> tag to allow users to skip the header and go straight to the main content. This is useful for screen readers and keyboard navigation.' => 'スキップリンクを有効にし、コンテンツを<code>&lt;main&gt;</code>タグでラップして、ユーザーがヘッダーをスキップしてメインコンテンツに直接移動できるようにします。スクリーンリーダーやキーボードナビゲーションに便利です。',
            'Skip Link Text' => 'スキップリンクのテキスト',
            'Filter elements…' => '要素をフィルター…',
            'Hidden Elements' => '非表示の要素',
            'Select All' => 'すべて選択',
            'Clear All' => 'すべてクリア',
            '<h2>Elements</h2>' => '<h2>要素</h2>',
            'id="enable_skip_link"> Enable' => 'id="enable_skip_link"> 有効化',
            'value="Save Changes"' => 'value="変更を保存"',
        ],
    ];
}
