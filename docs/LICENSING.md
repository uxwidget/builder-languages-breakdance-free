# Builder Languages for Breakdance — Licensing

## Modelo

Produto **premium-only**. Não há tier gratuito permanente.

Licenciamento comercial via **Freemius**.

## Identidade Freemius (não renomear sem migração)

| Campo | Valor |
| --- | --- |
| Nome exibido | Builder Languages for Breakdance |
| Slug Freemius | `breakdance-languages` |
| Text Domain | `breakdance-languages` |
| Arquivo principal (pasta atual) | `builder-languages-breakdance.php` |

O slug antigo permanece para não invalidar installs, webhooks e catálogos `breakdance-languages-*.po`.

## Setup local

1. SDK em `vendor/freemius/` (não versionar secrets).
2. Copie `config/freemius.config.example.php` → `config/freemius.php` (gitignored).
3. Siga [FREEMIUS-SETUP.md](./FREEMIUS-SETUP.md).

## Builds

- ZIP público / Freemius: sem `.blb-secret`, sem `config/freemius.php` de dev (ver `.distignore`).
- `.update.blb` pode ir no release; o secret de assinatura não.

## Ambientes

Não misture build de venda e pasta de desenvolvimento no **mesmo** WordPress. Use um site Local só para a versão comercial. Ver [AMBIENTES.md](./AMBIENTES.md) e [ATUALIZACOES.md](./ATUALIZACOES.md).

## Marca

Breakdance é marca de seus respectivos donos. **Builder Languages for Breakdance** é produto independente da UX Widget, sem afiliação ou endosso do Breakdance.
