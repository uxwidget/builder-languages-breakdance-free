# Builder Languages for Breakdance — Changelog

## ux-0.1.3

- Tela **Breakdance → Languages** passa a exibir os textos do próprio plugin no idioma escolhido (inclui Hindi e demais locales que caíam em inglês).
- Catálogo movido para `config/settings-ui-strings.json` (18 locales).

## ux-0.1.2

- Traduz categorias do painel Adicionar elementos (`Basic`, `Blocks`, `Site`, `Advanced`, `Dynamic`, `Forms`, `Other`) em **todos** os idiomas de produto.
- Corrige Hindi e demais locales onde essas labels ficavam em inglês (hardcoded no Breakdance, sem gettext).
- Fonte: `config/element-category-labels.json` + `includes/element-categories-i18n.php`.

## ux-0.1.1

- Após instalar o **pacote de idioma do WordPress** (não o idioma do Breakdance), a tela mostra o botão **Atualizar página** para recarregar o admin e aplicar as traduções.
- O botão **Atualizar** ao lado do seletor do builder também fica visível nesse momento.
- Manifesto `.update.blb` regenerado para `ux-0.1.1`.

## ux-0.1.0

Release inicial comercializável (nome de exibição: **Builder Languages for Breakdance**).

### Qualidade de placeholders (pós-MT)

- QA de placeholders: **0** em todos os locales suportados (não só o gate).
- Novo `scripts/fix-mt-placeholder-residuals.py` (cola árabe, `%s` corrompido, aspas, `%N$` sem tipo).
- `qa-placeholders.py` reconhece aspas tipográficas, CJK/Devanagari e compostos com hífen (menos falso positivo).
- JSON do Builder regenerado a partir dos PO corrigidos.

### Produto e marca

- Nome exibido e pasta: `builder-languages-breakdance` (evita conflito de marca com “Breakdance Languages”).
- Text domain / Freemius slug mantidos como `breakdance-languages` (compatibilidade de licença e PO do painel).
- Repositório GitHub privado: `marceloadias/builder-languages-breakdance`.
- Manifesto assinado `.update.blb` + tooling `scripts/blb-manifest.py` (secret local `.blb-secret`, nunca no Git).

### Idiomas (17 de produto)

- Gate: `pt_BR`, `pt_PT`, `it_IT`.
- Beta / MT: `fr_FR`, `de_DE`, `es_ES`, `es_LA`, `nl_NL`, `pl_PL`, `ru_RU`, `ar`, `he_IL`, `hi_IN`, `ja_JP`, `ko_KR`, `zh_CN`.
- Baseline: `en_US`, `en_GB`.
- Registry central: `config/supported-locales.json` + `includes/locale-registry.php`.
- Geolocalização Freemius: `config/freemius-geolocation-locales.json`.

### Form Builder e runtime

- `includes/form-builder-i18n.php` — filtro `breakdance_element_controls`.
- Overrides de editor (MutationObserver / AJAX / Pinia) em `includes/editor-overrides.php`.
- Prioridade Hindi/Hebraico: `includes/priority-locale-i18n.php` + configs JSON.
- Suporte RTL: `includes/rtl-support.php`.

### Infraestrutura

- Camada de compatibilidade JSON do Builder (`breakdance_i18n_json`).
- Gettext em `plugins_loaded` prioridade 20 (sobrescreve defaults do Breakdance).
- Guard de dependência Breakdance + header `Requires Plugins`.
- Scaffold Freemius para builds comerciais.
- QA: `scripts/validate-all.py`, `qa-placeholders.py`, `.distignore`.
- Diagnósticos em `Breakdance > Languages`.

### Documentação

- Docs e README em português do Brasil.
- Índice: `docs/INDEX.md`.
- Relatório da onda 10/07/2026: `docs/RELATORIO-ATUALIZACOES-2026-07-10.md`.
