<?php
/**
 * Builder Languages for Breakdance — Editor overrides.
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

add_action('unofficial_i_am_kevin_geary_master_of_all_things_css_and_html', 'breakdance_languages_print_editor_text_overrides', 5);
add_action('send_headers', 'breakdance_languages_send_builder_nocache_headers');

/**
 * Prevent cached builder HTML from skipping runtime translation scripts.
 */
function breakdance_languages_send_builder_nocache_headers(): void
{
    if (!breakdance_languages_is_builder_page()) {
        return;
    }

    if (headers_sent()) {
        return;
    }

    nocache_headers();
}

/**
 * Exclude Breakdance builder URLs from WP Rocket page cache.
 *
 * @param array<int, string> $uris
 * @return array<int, string>
 */
function breakdance_languages_reject_builder_from_wp_rocket(array $uris): array
{
    $uris[] = '/(.*)[?&]breakdance=builder(.*)/';
    $uris[] = '/(.*)[?&]oxygen=builder(.*)/';

    return $uris;
}

add_filter('rocket_cache_reject_uri', 'breakdance_languages_reject_builder_from_wp_rocket');
add_filter('rocket_exclude_defer_js', static function ($excluded): array {
    if (!is_array($excluded)) {
        $excluded = [];
    }

    if (breakdance_languages_is_builder_page()) {
        $excluded[] = 'breakdance-languages';
    }

    return $excluded;
});

/**
 * Whether the current request is the Breakdance builder SPA.
 */
function breakdance_languages_is_builder_page(): bool
{
    return breakdance_languages_freemius_is_builder_request();
}

/**
 * Resolve the locale used for editor label overrides.
 */
function breakdance_languages_resolve_editor_locale(): ?string
{
    $supported = function_exists('breakdance_languages_registry_editor_override_locales')
        ? breakdance_languages_registry_editor_override_locales()
        : ['pt_BR', 'pt_PT', 'ja_JP'];
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
 * Whether editor overrides should load on the current builder request.
 */
function breakdance_languages_should_inject_editor_overrides(): bool
{
    if (!breakdance_languages_is_builder_page()) {
        return false;
    }

    if (breakdance_languages_is_licensed() || breakdance_languages_can_apply_translations()) {
        return true;
    }

    return breakdance_languages_is_local_dev_site();
}

/**
 * @return array<string, array<string, string>>
 */
function breakdance_languages_editor_label_maps(): array
{
    return [
        'pt_BR' => [
            'Basic' => 'Básico',
            'Blocks' => 'Blocos',
            'Site' => 'Site',
            'Advanced' => 'Avançado',
            'Dynamic' => 'Dinâmico',
            'Forms' => 'Formulários',
            'Elements' => 'Elementos',
            'Global Styles' => 'Estilos globais',
            'Settings' => 'Configurações',
            'Design' => 'Design',
            'Content' => 'Conteúdo',
            'Typography' => 'Tipografia',
            'Size' => 'Tamanho',
            'Spacing' => 'Espaçamento',
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
            'Heading' => 'Título',
            'Section' => 'Seção',
            'Div' => 'Div',
            'Button' => 'Botão',
            'Columns' => 'Colunas',
            'Column' => 'Coluna',
            'Text' => 'Texto',
            'Text Link' => 'Link de texto',
            'Rich Text' => 'Texto rico',
            'Image V1' => 'Imagem V1',
            'Icon' => 'Ícone',
            'Grid' => 'Grade',
            'Missing Element' => 'Elemento ausente',
            'This Website' => 'Este site',
            'Styling' => 'Estilo',
            'Radius' => 'Raio',
            'Shadow' => 'Sombra',
            'Fronteiras' => 'Bordas',
            'Solid' => 'Sólido',
            'Dashed' => 'Tracejado',
            'Dotted' => 'Pontilhado',
            'Double' => 'Duplo',
            'Groove' => 'Ranhura',
            'Ridge' => 'Relevo',
            'Inset' => 'Entalhe',
            'Outset' => 'Elevado',
            'None' => 'Nenhum',
            'Outline' => 'Contorno',
            'Widget' => 'Widget do WordPress',
            'Widgets' => 'Widgets',
            'Current' => 'Atual',
            'Form' => 'Formulário',
            'Abra element' => 'Abrir elemento',
            'Abrir element' => 'Abrir elemento',
            'Novo element' => 'Novo elemento',
            'Abra macro' => 'Abrir macro',
            'Abrir macro' => 'Abrir macro',
            'Novo macro' => 'Novo macro',
            'Abra preset' => 'Abrir predefinição',
            'Abrir preset' => 'Abrir predefinição',
            'Novo preset' => 'Nova predefinição',
            'Criar novo element' => 'Criar novo elemento',
            'Criar novo macro' => 'Criar novo macro',
            'Criar novo preset' => 'Criar nova predefinição',
            'Por favor, insira o nome do element.' => 'Por favor, insira o nome do elemento.',
            'Por favor, insira o nome do preset.' => 'Por favor, insira o nome da predefinição.',
            'Pesquisar elements' => 'Pesquisar elementos',
            'Pesquisar macros' => 'Pesquisar macros',
            'Pesquisar presets' => 'Pesquisar predefinições',
            'Nenhum suporte é fornecido. Por favor leia o documentação antes de usar.' => 'Nenhum suporte é fornecido. Leia a documentação antes de usar.',
            'Nenhum suporte é fornecido. Por favor leia o' => 'Nenhum suporte é fornecido. Leia a',
            'Salve suas alterações antes de fechar este element ou descarte-as.' => 'Salve suas alterações antes de fechar este elemento ou descarte-as.',
            'Salve suas alterações antes de fechar este preset ou descarte-as.' => 'Salve suas alterações antes de fechar esta predefinição ou descarte-as.',
        ],
        'pt_PT' => [
            'Basic' => 'Básico',
            'Blocks' => 'Blocos',
            'Site' => 'Site',
            'Advanced' => 'Avançado',
            'Dynamic' => 'Dinâmico',
            'Forms' => 'Formulários',
            'Elements' => 'Elementos',
            'Global Styles' => 'Estilos globais',
            'Settings' => 'Definições',
            'Design' => 'Design',
            'Content' => 'Conteúdo',
            'Typography' => 'Tipografia',
            'Size' => 'Tamanho',
            'Spacing' => 'Espaçamento',
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
            'Heading' => 'Título',
            'Section' => 'Secção',
            'Div' => 'Div',
            'Button' => 'Botão',
            'Columns' => 'Colunas',
            'Column' => 'Coluna',
            'Text' => 'Texto',
            'Text Link' => 'Ligação de texto',
            'Rich Text' => 'Texto rico',
            'Image V1' => 'Imagem V1',
            'Icon' => 'Ícone',
            'Grid' => 'Grelha',
            'Missing Element' => 'Elemento em falta',
            'Current' => 'Atual',
            'Abra element' => 'Abrir elemento',
            'Abrir element' => 'Abrir elemento',
            'Novo element' => 'Novo elemento',
            'Abra macro' => 'Abrir macro',
            'Abrir macro' => 'Abrir macro',
            'Novo macro' => 'Novo macro',
            'Abra preset' => 'Abrir predefinição',
            'Abrir preset' => 'Abrir predefinição',
            'Novo preset' => 'Nova predefinição',
            'Criar novo element' => 'Criar novo elemento',
            'Criar novo macro' => 'Criar novo macro',
            'Criar novo preset' => 'Criar nova predefinição',
            'Por favor, insira o nome do element.' => 'Por favor, introduza o nome do elemento.',
            'Por favor, insira o nome do preset.' => 'Por favor, introduza o nome da predefinição.',
            'Pesquisar elements' => 'Pesquisar elementos',
            'Pesquisar macros' => 'Pesquisar macros',
            'Pesquisar presets' => 'Pesquisar predefinições',
            'Nenhum suporte é fornecido. Por favor leia o documentação antes de usar.' => 'Não é fornecido suporte. Leia a documentação antes de utilizar.',
            'Nenhum suporte é fornecido. Por favor leia o' => 'Não é fornecido suporte. Leia a',
            'This Website' => 'Este site',
        ],
        'ja_JP' => [
            'Basic' => '基本',
            'Blocks' => 'ブロック',
            'Site' => 'サイト',
            'Advanced' => '高度',
            'Dynamic' => '動的',
            'Forms' => 'フォーム',
            'Elements' => '要素',
            'Global Styles' => 'グローバルスタイル',
            'Settings' => '設定',
            'Save Changes' => '変更を保存',
            'This Website' => 'このサイト',
            'Current' => '現在',
        ],
    ];
}

/**
 * @return array<string, string>
 */
function breakdance_languages_get_editor_label_dictionary(): array
{
    if (!breakdance_languages_should_inject_editor_overrides()) {
        return [];
    }

    $locale = breakdance_languages_resolve_editor_locale();

    if ($locale === null) {
        return [];
    }

    $maps = breakdance_languages_editor_label_maps();
    $dictionary = $maps[$locale] ?? [];

    if (function_exists('breakdance_languages_section_label_fallbacks')) {
        $dictionary = array_merge(
            breakdance_languages_section_label_fallbacks($locale),
            $dictionary
        );
    }

    if (function_exists('breakdance_languages_get_design_library_ui_dictionary')) {
        $dictionary = array_merge(
            breakdance_languages_get_design_library_ui_dictionary($locale),
            $dictionary
        );
    }

    // Breakdance often does sprintf(__('Add %s'), 'Color') with an untranslated name.
    foreach (['Color', 'Gradient', 'Palette', 'Palettes'] as $englishToken) {
        if (isset($dictionary[$englishToken])) {
            continue;
        }

        $translated = __($englishToken, 'breakdance');

        if (is_string($translated) && $translated !== '' && $translated !== $englishToken) {
            $dictionary[$englishToken] = $translated;
        }
    }

    /**
     * @param array<string, string> $dictionary
     */
    return apply_filters('breakdance_languages_editor_label_dictionary', $dictionary, $locale);
}

/**
 * Print JS overrides for builder-only UI labels that bypass wp.i18n.
 */
function breakdance_languages_print_editor_text_overrides(): void
{
    if (!breakdance_languages_is_builder_page()) {
        return;
    }

    $dictionary = breakdance_languages_get_editor_label_dictionary();
    $designLibraryBridge = function_exists('breakdance_languages_get_design_library_iframe_bridge_config')
        ? breakdance_languages_get_design_library_iframe_bridge_config()
        : null;
    $debug = breakdance_languages_is_debug_enabled();
    $diagnostics = [
        'licensed' => breakdance_languages_is_licensed(),
        'canApply' => breakdance_languages_can_apply_translations(),
        'preference' => breakdance_languages_get_user_builder_locale(),
        'locale' => breakdance_languages_resolve_editor_locale(),
        'dictionaryKeys' => count($dictionary),
        'localDev' => breakdance_languages_is_local_dev_site(),
    ];

    if ($dictionary === []) {
        if ($debug) {
            ?>
        <script>
        console.warn('[Breakdance Languages] plugin detectado, mas sem dicionario de guias.', <?php echo wp_json_encode($diagnostics); ?>);
        </script>
            <?php
        }
        return;
    }
    ?>
    <script>
    (function () {
        var dictionary = <?php echo wp_json_encode($dictionary); ?>;
        var editorLocale = <?php echo wp_json_encode(breakdance_languages_resolve_editor_locale()); ?>;
        var debug = <?php echo $debug ? 'true' : 'false'; ?>;

        function logInfo() {
            if (debug) {
                console.info.apply(console, arguments);
            }
        }

        function logWarn() {
            if (debug) {
                console.warn.apply(console, arguments);
            }
        }

        logInfo('[Breakdance Languages] ativo', <?php echo wp_json_encode($diagnostics); ?>);

        if (!dictionary || typeof dictionary !== 'object') {
            return;
        }

        var pendingNodes = [];
        var scheduled = false;
        var observer = null;

        function lookupTranslation(text) {
            var normalized = text.replace(/^[\s\u2022\u00b7•·\-]+/, '').trim();

            if (Object.prototype.hasOwnProperty.call(dictionary, normalized)) {
                return dictionary[normalized];
            }

            if (Object.prototype.hasOwnProperty.call(dictionary, text)) {
                return dictionary[text];
            }

            // Design Library / Fancy Sections: "Masks #2", "Animated Text #3"
            var numbered = normalized.match(/^(.*?)(\s+#\d+)$/);

            if (numbered) {
                var base = numbered[1].trim();
                var suffix = numbered[2];
                var translatedBase = null;

                if (Object.prototype.hasOwnProperty.call(dictionary, base)) {
                    translatedBase = dictionary[base];
                } else if (Object.prototype.hasOwnProperty.call(dictionary, base.toLowerCase())) {
                    translatedBase = dictionary[base.toLowerCase()];
                }

                if (translatedBase) {
                    return translatedBase + suffix;
                }
            }

            // Truncated titles: "Animated Background..."
            var ellipsis = normalized.match(/^(.*?)(\.\.\.|…)$/);

            if (ellipsis) {
                var ellipsisBase = ellipsis[1].replace(/\s+$/, '');
                var dots = ellipsis[2];
                var translatedEllipsisBase = null;

                if (Object.prototype.hasOwnProperty.call(dictionary, ellipsisBase)) {
                    translatedEllipsisBase = dictionary[ellipsisBase];
                } else if (Object.prototype.hasOwnProperty.call(dictionary, ellipsisBase.toLowerCase())) {
                    translatedEllipsisBase = dictionary[ellipsisBase.toLowerCase()];
                }

                if (translatedEllipsisBase) {
                    return translatedEllipsisBase + dots;
                }
            }

            return null;
        }

        function translatePartialText(text) {
            if (typeof text !== 'string' || text === '') {
                return text;
            }

            var direct = lookupTranslation(text.trim());

            if (direct) {
                return direct;
            }

            var next = text;

            // Leftovers from sprintf(__('Add %s'), 'Color') — label is translated, %s stays English.
            ['constrained proportions', 'cropped', 'Color', 'Gradient'].forEach(function (fragment) {
                var translated = lookupTranslation(fragment);

                if (!translated && window.wp && window.wp.i18n && typeof window.wp.i18n.__ === 'function') {
                    var viaI18n = window.wp.i18n.__(fragment, 'breakdance');

                    if (viaI18n && viaI18n !== fragment) {
                        translated = viaI18n;
                    }
                }

                if (translated && next.indexOf(fragment) !== -1) {
                    next = next.split(fragment).join(translated);
                }
            });

            var selectedMatch = next.match(/^(\d+)\s+selected$/i);

            if (selectedMatch && editorLocale && editorLocale.indexOf('pt') === 0) {
                var count = parseInt(selectedMatch[1], 10);
                next = count + ' selecionado' + (count === 1 ? '' : 's');
            }

            return next;
        }

        function patchAvailableMediaSizes(sizes) {
            if (!Array.isArray(sizes)) {
                return false;
            }

            var changed = false;

            sizes.forEach(function (size) {
                if (!size || typeof size !== 'object') {
                    return;
                }

                if (typeof size.label === 'string') {
                    var nextLabel = translateLabel(size.label);

                    if (nextLabel !== size.label) {
                        size.label = nextLabel;
                        changed = true;
                    }
                }

                if (typeof size.subLabel === 'string') {
                    var nextSubLabel = translatePartialText(size.subLabel);

                    if (nextSubLabel !== size.subLabel) {
                        size.subLabel = nextSubLabel;
                        changed = true;
                    }
                }
            });

            return changed;
        }

        function translateLabel(label) {
            if (typeof label !== 'string' || label === '') {
                return label;
            }

            var translated = lookupTranslation(label.trim());

            return translated || label;
        }

        function patchControlSections(sections) {
            if (!Array.isArray(sections)) {
                return false;
            }

            var changed = false;

            sections.forEach(function (control) {
                if (!control || typeof control !== 'object') {
                    return;
                }

                if (typeof control.label === 'string') {
                    var nextLabel = translateLabel(control.label);

                    if (nextLabel !== control.label) {
                        control.label = nextLabel;
                        changed = true;
                    }
                }

                if (control.options && Array.isArray(control.options.items)) {
                    control.options.items.forEach(function (item) {
                        if (!item || typeof item !== 'object') {
                            return;
                        }

                        if (typeof item.text === 'string') {
                            var nextText = translateLabel(item.text);

                            if (nextText !== item.text) {
                                item.text = nextText;
                                changed = true;
                            }
                        }

                        if (typeof item.label === 'string' && item.label !== 'Label') {
                            var nextItemLabel = translateLabel(item.label);

                            if (nextItemLabel !== item.label) {
                                item.label = nextItemLabel;
                                changed = true;
                            }
                        }
                    });
                }

                if (Array.isArray(control.children) && patchControlSections(control.children)) {
                    changed = true;
                }
            });

            return changed;
        }

        function patchElementControls(controls) {
            if (!controls || typeof controls !== 'object') {
                return false;
            }

            var changed = false;

            ['contentSections', 'designSections', 'settingsSections'].forEach(function (key) {
                if (patchControlSections(controls[key])) {
                    changed = true;
                }
            });

            return changed;
        }

        function patchBuilderElementsList(elements) {
            if (!Array.isArray(elements)) {
                return false;
            }

            var changed = false;

            elements.forEach(function (element) {
                if (!element || !element.controls) {
                    return;
                }

                if (patchElementControls(element.controls)) {
                    changed = true;
                }
            });

            return changed;
        }

        function patchAjaxPayload(payload) {
            if (!payload || typeof payload !== 'object') {
                return false;
            }

            var changed = false;

            if (patchBuilderElementsList(payload.elements)) {
                changed = true;
            }

            if (patchBuilderElementsList(payload.builderElements)) {
                changed = true;
            }

            if (patchAvailableMediaSizes(payload.availableMediaSizes)) {
                changed = true;
            }

            if (
                payload.globalSettingsControlsAndTemplate &&
                Array.isArray(payload.globalSettingsControlsAndTemplate.controls) &&
                patchControlSections(payload.globalSettingsControlsAndTemplate.controls)
            ) {
                changed = true;
            }

            return changed;
        }

        function patchConfigStoreGlobalSettings() {
            if (
                !window.Breakdance ||
                !window.Breakdance.stores ||
                !window.Breakdance.stores.configStore ||
                !window.Breakdance.stores.configStore.globalSettingsControlsAndTemplate ||
                !Array.isArray(window.Breakdance.stores.configStore.globalSettingsControlsAndTemplate.controls)
            ) {
                return false;
            }

            return patchControlSections(
                window.Breakdance.stores.configStore.globalSettingsControlsAndTemplate.controls
            );
        }

        function patchConfigStoreMediaSizes() {
            if (
                !window.Breakdance ||
                !window.Breakdance.stores ||
                !window.Breakdance.stores.configStore ||
                !Array.isArray(window.Breakdance.stores.configStore.availableMediaSizes)
            ) {
                return false;
            }

            return patchAvailableMediaSizes(window.Breakdance.stores.configStore.availableMediaSizes);
        }

        if (window.fetch) {
            var originalFetch = window.fetch.bind(window);

            function isBreakdanceAjaxRequest(input, init) {
                var url = '';
                var body = init && init.body;

                if (typeof input === 'string') {
                    url = input;
                } else if (input && typeof input.url === 'string') {
                    url = input.url;
                }

                if (
                    url.indexOf('_breakdance_doing_ajax') !== -1 ||
                    url.indexOf('breakdance_ajax_at_any_url') !== -1
                ) {
                    return true;
                }

                if (!body && input && typeof input.body !== 'undefined') {
                    body = input.body;
                }

                if (typeof body === 'string') {
                    return body.indexOf('breakdance_ajax_at_any_url') !== -1 ||
                        body.indexOf('action=breakdance') !== -1;
                }

                if (typeof URLSearchParams !== 'undefined' && body instanceof URLSearchParams) {
                    return body.has('breakdance_ajax_at_any_url') ||
                        ((body.get('action') || '').indexOf('breakdance') === 0);
                }

                if (typeof FormData !== 'undefined' && body instanceof FormData) {
                    return body.has('breakdance_ajax_at_any_url') ||
                        ((body.get('action') || '').toString().indexOf('breakdance') === 0);
                }

                return false;
            }

            window.fetch = function (input, init) {
                return originalFetch(input, init).then(function (response) {
                    if (!isBreakdanceAjaxRequest(input, init)) {
                        return response;
                    }

                    return response.clone().json().then(function (payload) {
                        if (!patchAjaxPayload(payload)) {
                            return response;
                        }

                        logInfo('[Breakdance Languages] guias traduzidas no AJAX');

                        var headers = new Headers();

                        response.headers.forEach(function (value, key) {
                            headers.append(key, value);
                        });

                        return new Response(JSON.stringify(payload), {
                            status: response.status,
                            statusText: response.statusText,
                            headers: headers
                        });
                    }).catch(function () {
                        return response;
                    });
                });
            };

            logInfo('[Breakdance Languages] interceptador AJAX instalado');
        }

        function getBuilderElementsMap() {
            if (
                window.Breakdance &&
                window.Breakdance.stores &&
                window.Breakdance.stores.configStore &&
                window.Breakdance.stores.configStore.elements
            ) {
                return window.Breakdance.stores.configStore.elements;
            }

            return null;
        }

        function patchBuilderElementStore() {
            var elements = getBuilderElementsMap();

            if (!elements) {
                return false;
            }

            var changed = false;

            Object.keys(elements).forEach(function (slug) {
                var element = elements[slug];

                if (!element || !element.controls) {
                    return;
                }

                if (patchElementControls(element.controls)) {
                    changed = true;
                }
            });

            if (changed) {
                scan(document.body);
            }

            return changed;
        }

        var storePatchAttempts = 0;
        var storeElementsPatched = false;
        var mediaSizesPatched = false;
        var globalSettingsPatched = false;
        var storePatchTimer = window.setInterval(function () {
            storePatchAttempts += 1;

            if (!storeElementsPatched && patchBuilderElementStore()) {
                storeElementsPatched = true;
                logInfo('[Breakdance Languages] guias traduzidas no store Pinia');
            }

            if (!mediaSizesPatched && patchConfigStoreMediaSizes()) {
                mediaSizesPatched = true;
                logInfo('[Breakdance Languages] tamanhos de mídia traduzidos no store Pinia');
            }

            if (!globalSettingsPatched && patchConfigStoreGlobalSettings()) {
                globalSettingsPatched = true;
                logInfo('[Breakdance Languages] estilos globais traduzidos no store Pinia');
                scan(document.body);
            }

            if (storeElementsPatched && mediaSizesPatched && globalSettingsPatched) {
                window.clearInterval(storePatchTimer);
                return;
            }

            if (storePatchAttempts >= 120) {
                window.clearInterval(storePatchTimer);
            }
        }, 250);

        function translateNode(node) {
            if (!node || node.nodeType !== Node.TEXT_NODE) {
                return;
            }

            var raw = node.nodeValue || '';
            var trimmed = raw.trim();

            if (!trimmed) {
                return;
            }

            var replacementText = lookupTranslation(trimmed) || translatePartialText(trimmed);

            if (!replacementText || replacementText === trimmed) {
                return;
            }

            var replacement = raw.replace(trimmed, replacementText);

            if (observer) {
                observer.disconnect();
            }

            node.nodeValue = replacement;

            if (observer) {
                observer.observe(document.body, {
                    childList: true,
                    subtree: true,
                    characterData: true
                });
            }
        }

        function scan(root) {
            if (!root || !root.querySelectorAll) {
                return;
            }

            var selectors = [
                '.controls-tree-linear-heading',
                '.control-section-as-popout-label',
                '.breakdance-control-wrapper-control-label'
            ];

            selectors.forEach(function (selector) {
                root.querySelectorAll(selector).forEach(function (node) {
                    translateNode(node.firstChild);
                });
            });

            var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
            var current;

            while ((current = walker.nextNode())) {
                translateNode(current);
            }
        }

        function flushPending() {
            scheduled = false;

            if (!pendingNodes.length) {
                return;
            }

            var batch = pendingNodes.slice();
            pendingNodes = [];

            batch.forEach(function (node) {
                if (node.nodeType === Node.TEXT_NODE) {
                    translateNode(node);
                    return;
                }

                if (node.nodeType === Node.ELEMENT_NODE) {
                    scan(node);
                }
            });
        }

        function scheduleFlush() {
            if (scheduled) {
                return;
            }

            scheduled = true;
            window.requestAnimationFrame(flushPending);
        }

        function queueNode(node) {
            if (!node) {
                return;
            }

            pendingNodes.push(node);
            scheduleFlush();
        }

        scan(document.body);

        observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === 'characterData') {
                    queueNode(mutation.target);
                    return;
                }

                mutation.addedNodes.forEach(function (node) {
                    queueNode(node);
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true
        });

        var designLibraryBridge = <?php echo wp_json_encode($designLibraryBridge); ?>;

        if (designLibraryBridge && designLibraryBridge.proxyBase) {
            function isDesignLibraryHost(url) {
                try {
                    var parsed = new URL(url, window.location.href);
                    return designLibraryBridge.allowedHosts.indexOf(parsed.hostname.toLowerCase()) !== -1;
                } catch (error) {
                    return false;
                }
            }

            function toDesignLibraryProxyUrl(url) {
                var params = new URLSearchParams();
                params.set('action', 'breakdance_languages_design_library_proxy');
                params.set('nonce', designLibraryBridge.nonce);
                params.set('url', url);
                return designLibraryBridge.proxyBase + '?' + params.toString();
            }

            function maybeProxyDesignLibraryIframe(iframe) {
                if (!iframe || iframe.dataset.bdLangProxied === '1') {
                    return;
                }

                var src = iframe.getAttribute('src');

                if (!src || src.indexOf('breakdance=design-library') === -1 || !isDesignLibraryHost(src)) {
                    return;
                }

                iframe.dataset.bdLangProxied = '1';
                iframe.setAttribute('src', toDesignLibraryProxyUrl(src));
            }

            function watchDesignLibraryIframes(root) {
                (root || document).querySelectorAll('iframe').forEach(maybeProxyDesignLibraryIframe);
            }

            watchDesignLibraryIframes(document.body);

            new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        if (!node) {
                            return;
                        }

                        if (node.tagName === 'IFRAME') {
                            maybeProxyDesignLibraryIframe(node);
                            return;
                        }

                        if (node.querySelectorAll) {
                            watchDesignLibraryIframes(node);
                        }
                    });
                });
            }).observe(document.body, {
                childList: true,
                subtree: true
            });

            logInfo('[Breakdance Languages] proxy da biblioteca de projetos ativo');
        }
    }());
    </script>
    <?php
}
