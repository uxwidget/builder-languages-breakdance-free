<?php
/**
 * Builder Languages for Breakdance — Form Builder i18n.
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

add_filter('breakdance_languages_editor_label_dictionary', 'breakdance_languages_merge_form_builder_editor_labels', 12, 2);
add_filter('breakdance_element_controls', 'breakdance_languages_translate_form_builder_controls', 100, 2);

/**
 * @return array<string, array<string, string>>
 */
function breakdance_languages_form_builder_ui_maps(): array
{
    return [
        'pt_BR' => [
            'Actions After Submission' => 'Ações após envio',
            'Store Submission' => 'Armazenar envio',
            'Submission Title' => 'Título do envio',
            'Store uploaded files' => 'Armazenar arquivos enviados',
            'Add uploaded files to WordPress media library' => 'Adicionar arquivos à biblioteca de mídia do WordPress',
            'Restrict uploaded file access to admin users' => 'Restringir acesso aos arquivos enviados a administradores',
            'Run Action Conditionally' => 'Executar ação condicionalmente',
            'Email' => 'E-mail',
            'Emails' => 'E-mails',
            'New contact form message' => 'Nova mensagem de formulário de contato',
            'Add email' => 'Adicionar e-mail',
            'Subject' => 'Assunto',
            'To Email' => 'E-mail de destino',
            'From Email' => 'E-mail de origem',
            'From Name' => 'Nome do remetente',
            'Reply To' => 'Responder para',
            'Attach uploaded files' => 'Anexar arquivos enviados',
            'BCC' => 'CCO',
            'Form Name' => 'Nome do formulário',
            'Add Field' => 'Adicionar campo',
            'Field' => 'Campo',
            'Error Message' => 'Mensagem de erro',
            'Hide Form On Success' => 'Ocultar formulário após sucesso',
            'Redirect After Submit' => 'Redirecionar após envio',
            'Form HTML ID' => 'ID HTML do formulário',
            'Submit HTML ID' => 'ID HTML do botão enviar',
            'Add Honeypot Field' => 'Adicionar campo honeypot',
            'Enable CSRF Protection' => 'Ativar proteção CSRF',
            'Enable reCAPTCHA' => 'Ativar reCAPTCHA',
            'Use reCAPTCHA API Key' => 'Usar chave de API do reCAPTCHA',
            'CSRF Disabled' => 'CSRF desativado',
            'CSRF protection can enhance the security of forms gated behind a login screen - ' => 'A proteção CSRF pode aumentar a segurança de formulários protegidos por login - ',
            'see details' => 'ver detalhes',
            'Archive Title' => 'Título do arquivo',
            'Disable Prefix' => 'Desativar prefixo',
            'Post Title' => 'Título da postagem',
            'Add Tab' => 'Adicionar aba',
            'Add Condition' => 'Adicionar condição',
            'No action selected' => 'Nenhuma ação selecionada',
            'All Fields' => 'Todos os campos',
        ],
        'pt_PT' => [
            'Actions After Submission' => 'Ações após envio',
            'Store Submission' => 'Armazenar submissão',
            'Submission Title' => 'Título da submissão',
            'Store uploaded files' => 'Armazenar ficheiros enviados',
            'Add uploaded files to WordPress media library' => 'Adicionar ficheiros à biblioteca multimédia do WordPress',
            'Run Action Conditionally' => 'Executar ação condicionalmente',
            'Email' => 'E-mail',
            'Emails' => 'E-mails',
            'New contact form message' => 'Nova mensagem de formulário de contacto',
            'Add email' => 'Adicionar e-mail',
            'Subject' => 'Assunto',
            'To Email' => 'E-mail de destino',
            'From Email' => 'E-mail de origem',
            'From Name' => 'Nome do remetente',
            'Reply To' => 'Responder para',
            'Attach uploaded files' => 'Anexar ficheiros enviados',
            'BCC' => 'BCC',
            'Form Name' => 'Nome do formulário',
            'Add Field' => 'Adicionar campo',
            'Error Message' => 'Mensagem de erro',
            'Hide Form On Success' => 'Ocultar formulário após sucesso',
            'Form HTML ID' => 'ID HTML do formulário',
            'Submit HTML ID' => 'ID HTML do botão enviar',
            'Add Honeypot Field' => 'Adicionar campo honeypot',
            'Enable CSRF Protection' => 'Ativar proteção CSRF',
            'Enable reCAPTCHA' => 'Ativar reCAPTCHA',
            'CSRF protection can enhance the security of forms gated behind a login screen - ' => 'A proteção CSRF pode aumentar a segurança de formulários protegidos por login - ',
            'see details' => 'ver detalhes',
            'Archive Title' => 'Título do arquivo',
            'Disable Prefix' => 'Desativar prefixo',
            'Post Title' => 'Título da publicação',
            'Add Tab' => 'Adicionar separador',
            'Add Condition' => 'Adicionar condição',
            'No action selected' => 'Nenhuma ação selecionada',
            'All Fields' => 'Todos os campos',
        ],
        'ja_JP' => [
            'Actions After Submission' => '送信後のアクション',
            'Store Submission' => '送信を保存',
            'Submission Title' => '送信タイトル',
            'Store uploaded files' => 'アップロードファイルを保存',
            'Add uploaded files to WordPress media library' => 'アップロードファイルをメディアライブラリに追加',
            'Run Action Conditionally' => '条件付きでアクションを実行',
            'Email' => 'メール',
            'Emails' => 'メール',
            'New contact form message' => '新しいお問い合わせメッセージ',
            'Add email' => 'メールを追加',
            'Subject' => '件名',
            'To Email' => '送信先メール',
            'From Email' => '送信元メール',
            'From Name' => '送信者名',
            'Reply To' => '返信先',
            'Attach uploaded files' => 'アップロードファイルを添付',
            'Form Name' => 'フォーム名',
            'Add Field' => 'フィールドを追加',
            'Error Message' => 'エラーメッセージ',
            'Hide Form On Success' => '成功時にフォームを非表示',
            'Form HTML ID' => 'フォームHTML ID',
            'Submit HTML ID' => '送信ボタンHTML ID',
            'Add Honeypot Field' => 'ハニーポットフィールドを追加',
            'Enable CSRF Protection' => 'CSRF保護を有効化',
            'Enable reCAPTCHA' => 'reCAPTCHAを有効化',
            'CSRF protection can enhance the security of forms gated behind a login screen - ' => 'CSRF保護はログイン画面の背後にあるフォームのセキュリティを強化できます - ',
            'see details' => '詳細を見る',
            'Archive Title' => 'アーカイブタイトル',
            'Disable Prefix' => 'プレフィックスを無効化',
            'Post Title' => '投稿タイトル',
            'Add Tab' => 'タブを追加',
            'Add Condition' => '条件を追加',
            'No action selected' => 'アクション未選択',
            'All Fields' => 'すべてのフィールド',
        ],
        'he_IL' => [
            'Actions After Submission' => 'פעולות לאחר שליחה',
            'Store Submission' => 'שמירת שליחה',
            'Submission Title' => 'כותרת שליחה',
            'Store uploaded files' => 'שמירת קבצים שהועלו',
            'Add uploaded files to WordPress media library' => 'הוספת קבצים לספריית המדיה',
            'Run Action Conditionally' => 'הפעלת פעולה בתנאי',
            'Email' => 'דוא"ל',
            'Emails' => 'הודעות דוא"ל',
            'Add email' => 'הוספת דוא"ל',
            'Subject' => 'נושא',
            'To Email' => 'דוא"ל נמען',
            'From Email' => 'דוא"ל שולח',
            'From Name' => 'שם שולח',
            'Reply To' => 'השב אל',
            'Attach uploaded files' => 'צירוף קבצים שהועלו',
            'Form Name' => 'שם הטופס',
            'Add Field' => 'הוספת שדה',
            'Error Message' => 'הודעת שגיאה',
            'Hide Form On Success' => 'הסתרת טופס לאחר הצלחה',
            'Form HTML ID' => 'מזהה HTML של הטופס',
            'Submit HTML ID' => 'מזהה HTML של כפתור שליחה',
            'Add Honeypot Field' => 'הוספת שדה honeypot',
            'Enable CSRF Protection' => 'הפעלת הגנת CSRF',
            'Enable reCAPTCHA' => 'הפעלת reCAPTCHA',
            'Archive Title' => 'כותרת ארכיון',
            'Disable Prefix' => 'השבתת קידומת',
            'Post Title' => 'כותרת פוסט',
            'Add Tab' => 'הוספת לשונית',
            'No action selected' => 'לא נבחרה פעולה',
            'CSRF protection can enhance the security of forms gated behind a login screen - ' => 'הגנת CSRF יכולה לשפר את אבטחת טפסים המוגנים בכניסה - ',
            'see details' => 'ראה פרטים',
        ],
        'ar' => [
            'Archive Title' => 'عنوان الأرشيف',
            'Disable Prefix' => 'تعطيل البادئة',
            'Post Title' => 'عنوان المشاركة',
            'Post Content' => 'محتوى المشاركة',
            'Post Excerpt' => 'مقتطف المشاركة',
            'Add Tab' => 'إضافة تبويب',
            'Add Condition' => 'إضافة شرط',
            'No action selected' => 'لم يتم اختيار إجراء',
            'All Fields' => 'كل الحقول',
        ],
    ];
}

/**
 * @return array<string, string>
 */
function breakdance_languages_get_form_builder_label_map(?string $locale = null): array
{
    $locale = $locale ?: breakdance_languages_resolve_locale();

    if ($locale === null) {
        return [];
    }

    $map = breakdance_languages_form_builder_ui_maps()[$locale] ?? [];

    if ($locale === 'hi_IN' && function_exists('breakdance_languages_get_priority_locale_strings')) {
        $map = array_merge(breakdance_languages_get_priority_locale_strings('hi_IN'), $map);
    }

    if ($locale === 'he_IL' && function_exists('breakdance_languages_get_priority_locale_strings')) {
        $map = array_merge(breakdance_languages_get_priority_locale_strings('he_IL'), $map);
    }

    return $map;
}

/**
 * @return array<string, string>
 */
function breakdance_languages_get_form_builder_ui_dictionary(?string $locale = null): array
{
    if (!breakdance_languages_should_inject_editor_overrides()) {
        return [];
    }

    return breakdance_languages_get_form_builder_label_map($locale);
}

/**
 * Translate Form Builder controls that bypass gettext in Breakdance core.
 *
 * @param array<string, mixed> $controls
 * @param object               $element
 * @return array<string, mixed>
 */
function breakdance_languages_translate_form_builder_controls(array $controls, $element): array
{
    unset($element);

    if (!breakdance_languages_can_apply_translations() || !breakdance_languages_is_builder_runtime_request()) {
        return $controls;
    }

    $dictionary = breakdance_languages_get_form_builder_label_map();

    if ($dictionary === []) {
        return $controls;
    }

    return breakdance_languages_apply_form_builder_dictionary_recursive($controls, $dictionary);
}

/**
 * @param array<array-key, mixed> $controls
 * @param array<string, string>   $dictionary
 * @return array<array-key, mixed>
 */
function breakdance_languages_apply_form_builder_dictionary_recursive(array $controls, array $dictionary): array
{
    foreach ($controls as $index => $control) {
        if (!is_array($control)) {
            continue;
        }

        if (isset($control['label']) && is_string($control['label']) && $control['label'] !== '') {
            $control['label'] = $dictionary[$control['label']] ?? $control['label'];
        }

        if (!isset($control['options']) || !is_array($control['options'])) {
            $controls[$index] = $control;
            continue;
        }

        if (
            isset($control['options']['placeholder'])
            && is_string($control['options']['placeholder'])
            && $control['options']['placeholder'] !== ''
        ) {
            $control['options']['placeholder'] = $dictionary[$control['options']['placeholder']]
                ?? $control['options']['placeholder'];
        }

        foreach (['buttonName', 'defaultTitle'] as $option_key) {
            if (
                isset($control['options'][$option_key])
                && is_string($control['options'][$option_key])
                && $control['options'][$option_key] !== ''
            ) {
                $control['options'][$option_key] = $dictionary[$control['options'][$option_key]]
                    ?? $control['options'][$option_key];
            }
        }

        if (
            isset($control['options']['content'])
            && is_string($control['options']['content'])
            && $control['options']['content'] !== ''
        ) {
            $control['options']['content'] = breakdance_languages_translate_form_builder_html_snippets(
                $control['options']['content'],
                $dictionary
            );
        }

        if (
            isset($control['options']['alertBoxOptions']['content'])
            && is_string($control['options']['alertBoxOptions']['content'])
            && $control['options']['alertBoxOptions']['content'] !== ''
        ) {
            $control['options']['alertBoxOptions']['content'] = breakdance_languages_translate_form_builder_html_snippets(
                $control['options']['alertBoxOptions']['content'],
                $dictionary
            );
        }

        if (isset($control['options']['items']) && is_array($control['options']['items'])) {
            foreach ($control['options']['items'] as $item_index => $item) {
                if (!is_array($item)) {
                    continue;
                }

                foreach (['label', 'text'] as $item_key) {
                    if (isset($item[$item_key]) && is_string($item[$item_key]) && $item[$item_key] !== '') {
                        $item[$item_key] = $dictionary[$item[$item_key]] ?? $item[$item_key];
                    }
                }

                $control['options']['items'][$item_index] = $item;
            }
        }

        if (isset($control['children']) && is_array($control['children'])) {
            $control['children'] = breakdance_languages_apply_form_builder_dictionary_recursive(
                $control['children'],
                $dictionary
            );
        }

        $controls[$index] = $control;
    }

    return $controls;
}

/**
 * @param array<string, string> $dictionary
 */
function breakdance_languages_translate_form_builder_html_snippets(string $html, array $dictionary): string
{
    $next = $html;

    foreach ($dictionary as $source => $target) {
        if ($source === '' || strpos($next, $source) === false) {
            continue;
        }

        $next = str_replace($source, $target, $next);
    }

    return $next;
}

/**
 * @param array<string, string> $dictionary
 * @return array<string, string>
 */
function breakdance_languages_merge_form_builder_editor_labels(array $dictionary, ?string $locale): array
{
    $labels = breakdance_languages_get_form_builder_ui_dictionary($locale);

    if ($labels === []) {
        return $dictionary;
    }

    return array_merge($dictionary, $labels);
}
