# Builder Languages for Breakdance — Compatibilidade

## Escopo

O plugin é feito para o **Breakdance Builder** e os **Breakdance Elements** first-party.

Não substitui language packs de:

- WordPress core
- WooCommerce (text domain `woocommerce`)
- Temas ou plugins de terceiros

## Requisitos

| Item | Mínimo |
| --- | --- |
| WordPress | 6.0+ |
| PHP | 7.4+ |
| Breakdance | instalado e ativo (`Requires Plugins: breakdance`) |

## Camadas de tradução

| Camada | Mecanismo |
| --- | --- |
| Admin / PHP | gettext `breakdance` + `breakdance-elements` (`.po` / `.mo`) |
| Builder JS | filtro `breakdance_i18n_json` + JSON |
| Controles / Form Builder | filtro `breakdance_element_controls` + overrides JS |
| Painel do plugin | text domain `breakdance-languages` |

Strings hardcoded em JS compilado ou vendor podem exigir patch de runtime ou ficarem em inglês até o Breakdance expor gettext.

## Locales

Fonte da verdade: `config/supported-locales.json`.

- **Gate de release:** `pt_BR`, `pt_PT`, `it_IT`
- **Aliases:** `es_419` / `es_MX` → `es_LA`; `ja` → `ja_JP`; `zh` → `zh_CN`; `he` / `iw` → `he_IL`
- **RTL:** `ar`, `he_IL`

## Freemius

Licensing comercial via Freemius (slug interno `breakdance-languages`). Ativa só com SDK + `config/freemius.php` (local / build comercial).

## Manifesto BLB

`.update.blb` é metadado interno UX Widget. Não substitui o update Freemius. Ver [BLB-MANIFEST.md](./BLB-MANIFEST.md).

## Limites conhecidos

- Resíduos de MT em locales beta (placeholders, termos curtos)
- WooCommerce Breakdance subplugin → language packs Woo
- Conteúdo do banco não é traduzido automaticamente
