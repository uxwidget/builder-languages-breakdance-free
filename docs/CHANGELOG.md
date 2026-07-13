# Builder Languages for Breakdance — Changelog

## 0.1.14

- Restaura **English (United States)** no seletor: volta ao inglês nativo do Breakdance (sem interceptar packs).
- Corrige nomes de elemento **Post Title** / **Archive Title** / **HTML IMG** (e wrappers) em FR, DE, ES, ES-LA, JA, IT, PT-PT, KO, HE.
- Popup de reload do builder traduzido; remendos AR (Post Title, Wrapper Link, Color/`Add %s`); Fancy Sections set names com sufixo `#N`.
- Design Library / demos externos fora de escopo (URL remota do Breakdance).

## 0.1.13

- Popup de “idioma atualizado / recarregar” no builder traduzido nos 18 locales (não fica mais fixo em português).
- Design Library (Samba etc.): mapa UI ampliado para `fr_FR`, `de_DE`, `es_ES`, `es_LA` e `ar` (View Sections, categorias, botões).
- Árabe: nomes de elemento **Post Title** / **Archive Title** e campos dinâmicos Post Content / Excerpt corrigidos; override no Form Builder.

## 0.1.12

- **Freemius:** Public Key correta `pk_e984dedde8057992b2e0735383e70` (a `pk_0b7bc…` antiga causava *"Plugin does not exist"*).
- **Versão SemVer** `0.1.12` (Freemius rejeita prefixo `ux-` no Deploy).
- Languages sem licença abre Freemius na hora; com licença mantém painel de status (Licença Ativa / Produção).
- Correção: menu License não remove `$submenu` (evita “not allowed to access this page”).
- Connect Freemius: nome do nosso plugin, texto em 2 linhas centralizado + link “Can't find…” centralizado.
- Aviso amarelo próprio de licença removido — permanece o nag verde do Freemius (alinhado ao Breakdance).
- Canal local `BREAKDANCE_LANGUAGES_CHANNEL` (`sales`|`dev`) + badge na admin bar.
- ZIP comercial Released no Freemius (versão anterior removida do Deploy).

## ux-0.1.11

- ZIP comercial sem pasta `docs/`, sem `.update.blb`, sem `.gitkeep` / README de desenvolvimento.
- Cabeçalhos padrão UX Widget nos PHP do plugin; `LICENSE.md` (GPL + Freemius).
- Description e `License: GPLv2 or later` no header WordPress.
- Mitigações de licença: sem `.dev`/`.staging` como local; `RELEASE_BUILD` no ZIP; cache TTL 6h; inferência conservadora.
- Design Library proxy: HTTPS-only, strip de scripts remotos, CSP.
- Removido asset órfão `builder-language-header.png`.
- Nome do ZIP estável: `builder-languages-breakdance.zip` (versão só no plugin).

## ux-0.1.6

- Remove `en_US` dos locales suportados e dos catálogos (`breakdance-*-en_US.*`): o Breakdance já é nativo nesse idioma.
- Mantém `en_GB` como **English International** (único inglês do plugin).
- Sem match de locale (ex.: perfil `en_US`), o plugin não aplica pacote — não força `en_GB`.
- Scripts de geração passam a usar `en_GB` como source padrão.

## ux-0.1.5

- Script `scripts/pack-release.py` gera ZIP comercial respeitando `.distignore` (sem scripts, marketing, cache nem secrets).
- Guia [PACK-RELEASE.md](./PACK-RELEASE.md); `dist/` no `.gitignore`.

## ux-0.1.4

- Fallback de locale só aceita idiomas com catálogos presentes (`.po`/`.mo`/`.json`); `en_US`↔`en_GB` quando o pack listado não tem arquivos.
- Espelhamento JED `breakdance-elements` → `breakdance` não sobrescreve chaves já existentes no domínio principal.
- Manifesto `.update.blb` regenerado para `ux-0.1.4`.

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
