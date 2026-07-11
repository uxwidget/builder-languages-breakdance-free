# Builder Languages for Breakdance

Plugin complementar para WordPress que carrega pacotes de idioma para a interface do Breakdance Builder, telas de admin/PHP do Breakdance e elementos first-party.

O Breakdance Builder precisa estar instalado e ativo. Se o Builder Languages for Breakdance estiver ativo sem o Breakdance, o plugin exibe um aviso no admin e não tenta carregar os arquivos de idioma.

## Idiomas incluídos

17 idiomas de produto, com inglês americano como baseline padrão:

- `pt_BR` — Português (Brasil)
- `pt_PT` — Português (Portugal)
- `it_IT` — Italiano
- `fr_FR` — Francês
- `de_DE` — Alemão
- `es_ES` — Espanhol (Espanha)
- `es_LA` — Espanhol (América Latina)
- `nl_NL` — Holandês
- `pl_PL` — Polonês
- `ru_RU` — Russo
- `ar` — Árabe
- `he_IL` — Hebraico (RTL)
- `hi_IN` — Hindi
- `ja_JP` — Japonês
- `ko_KR` — Coreano
- `zh_CN` — Chinês (Simplificado)
- `en_GB` — Inglês (Internacional)
- `en_US` — Inglês (Estados Unidos) — baseline e fallback em runtime

Gate de release: `pt_BR`, `pt_PT` e `it_IT` devem passar com **0** placeholders suspeitos.

## Como funciona

O Breakdance entrega traduções do Builder via `wp.i18n` e expõe o filtro `breakdance_i18n_json`. Este plugin usa esse filtro e mescla os arquivos `languages/breakdance-{locale}.json` e `languages/breakdance-elements-{locale}.json` no payload de tradução do Builder.

No Breakdance 2.8.0, o JSON filtrado do Builder é registrado no domínio JavaScript fixo `breakdance`. Por compatibilidade, o plugin também espelha as traduções JSON de `breakdance-elements` no domínio `breakdance`, preservando os dados originais do domínio `breakdance-elements`.

As traduções de admin/PHP do Breakdance são carregadas como gettext padrão com `languages/breakdance-{locale}.po` e `languages/breakdance-{locale}.mo` no text domain original `breakdance`. O carregamento ocorre em `plugins_loaded` com prioridade 20, para poder sobrescrever o carregamento padrão do Breakdance.

As traduções dos elementos first-party usam o text domain `breakdance-elements` com `languages/breakdance-elements-{locale}.po` e `.mo`.

As regras de fallback de locale ficam em `translation-fallbacks.json`. Sem correspondência exata, o plugin cai para `en_US` (inglês americano).

## Idioma do perfil WordPress vs idioma do Builder

O plugin usa **duas camadas de idioma**:

| Camada | Onde configurar | O que controla |
| --- | --- | --- |
| Idioma do perfil WordPress | `Usuários > Perfil > Idioma` | Locale base do WordPress e partes do Breakdance |
| Idioma do Builder | `Breakdance > Languages` | Idioma preferido do Breakdance Builder |

### Idioma explícito do Builder

Ao escolher um **idioma específico** em `Breakdance > Languages` (não Auto), o plugin também atualiza o **locale do perfil WordPress** do usuário atual. Assim Breakdance, WordPress e o builder ficam no mesmo idioma, sem precisar ir em `Usuários > Perfil > Idioma`.

### Modo Auto

Se você escolher **Usar idioma do perfil WordPress**, o builder segue o locale do perfil. Altere o idioma do perfil para mudar o builder no modo Auto.

### Depois de mudar o idioma

Recarregue o Breakdance Builder manualmente após salvar (`Ctrl+Shift+R` ou feche e reabra a aba do builder). Uma aba aberta do builder não é recarregada automaticamente.

A tela de Languages mostra o idioma atual do perfil WordPress e se ele coincide com a escolha do builder.

Veja também: `docs/USER-GUIDE.md`, `docs/FAQ.md` e `docs/TROUBLESHOOTING.md`.

Diagnósticos ficam em WordPress em `Breakdance > Languages`.

O licensing Freemius está preparado para builds comerciais e só ativa quando as constantes do produto e o SDK estão presentes.

## Status das traduções

O plugin inclui catálogos editáveis gerados a partir dos `.pot` do Breakdance:

- Admin/PHP: `breakdance/languages/breakdance.pot` → `breakdance-{locale}.po/.mo`
- Builder JS: `breakdance/languages/breakdance-builder.pot` → `breakdance-builder-{locale}.po` → `breakdance-{locale}.json`
- Elements PHP/runtime: `breakdance/subplugins/breakdance-elements/languages/breakdance-elements.pot` → `breakdance-elements-{locale}.po/.mo`
- Controles de elementos: `breakdance/subplugins/breakdance-elements/languages/breakdance-elements-builder.pot` → `breakdance-elements-{locale}.po/.mo` e `breakdance-elements-{locale}.json`

Strings do WooCommerce em `subplugins/breakdance-woocommerce` usam em geral o text domain `woocommerce`, então são traduzidas pelos language packs do WooCommerce, não por este plugin.

Algumas strings em JavaScript compilado ou código de vendor ainda podem estar hardcoded e ausentes dos `.pot`. Essas exigem camada de substituição em runtime ou fluxo de tradução assistido.

## Manifesto privado `.update.blb`

Metadados de versão/canal ficam no arquivo assinado `.update.blb` (seguro para o release). O segredo de assinatura (`.blb-secret`) **nunca** vai para o Git.

Detalhes: `docs/BLB-MANIFEST.md`.

## Documentação

Índice completo: [`docs/INDEX.md`](docs/INDEX.md).

- **Ambientes (dev vs venda — WP separado):** `docs/AMBIENTES.md`
- **Como documentar atualizações:** `docs/ATUALIZACOES.md`
- **ZIP comercial (`.distignore`):** `docs/PACK-RELEASE.md`
- **Changelog (fonte oficial):** `docs/CHANGELOG.md`
- **Setup de desenvolvimento (site Local limpo):** `docs/DEV-SETUP.md`
- Instalação: `docs/INSTALLATION.md`
- Guia do usuário: `docs/USER-GUIDE.md`
- FAQ: `docs/FAQ.md`
- Troubleshooting: `docs/TROUBLESHOOTING.md`
- Compatibilidade: `docs/COMPATIBILITY.md`
- Política de suporte: `docs/SUPPORT-POLICY.md`
- Rascunho de reembolso: `docs/REFUND-POLICY.md`
- Notas de licensing: `docs/LICENSING.md`
- Relatório de atualizações: `docs/RELATORIO-ATUALIZACOES-2026-07-10.md`

## Materiais de vendas

- Rascunho da página de vendas: `marketing/SALES-PAGE.md`
- Rascunho de preços: `marketing/PRICING.md`
- Checklist de lançamento: `marketing/LAUNCH-CHECKLIST.md`

## QA de release

- **Tudo em um:** `python scripts/validate-all.py`
- **ZIP comercial:** `python scripts/pack-release.py` (ver `docs/PACK-RELEASE.md`)
- Estrutura dos catálogos: `python scripts/verify-catalogues.py`
- Espaçamento de placeholders: `python scripts/qa-placeholders.py --summary --all-supported`
- Correção de espaçamento MT: `python scripts/fix-placeholder-spacing.py` (depois `--json-only` + `compile-mo.py`)
- Exclusões de distribuição: `.distignore`

**Gate de release:** `pt_BR`, `pt_PT` e `it_IT` devem mostrar **0** placeholders suspeitos. Outros locales são acompanhados e podem manter resíduos de MT (especialmente `de_DE`).
