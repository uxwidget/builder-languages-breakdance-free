# Builder Languages for Breakdance — Troubleshooting

## A interface do Builder continua em inglês

1. Confirme que **Builder Languages for Breakdance** está ativo.
2. Confirme que o **Breakdance Builder** está ativo.
3. Em `Breakdance > Languages`, escolha o idioma e salve.
4. Recarregue o builder (`Ctrl+Shift+R` ou feche/reabra a aba).
5. Limpe cache de plugin, servidor e CDN.
6. Verifique se o locale está em `config/supported-locales.json`.

## Só parte da interface está traduzida

Esperado em alguns casos:

- Strings hardcoded em JS compilado do Breakdance
- Plugins de terceiros / WooCommerce
- Conteúdo do banco (títulos, textos de formulário salvos)

O Form Builder usa camadas extras (`form-builder-i18n.php` + overrides de editor). Se labels específicas falharem, reporte o texto exato em inglês e o locale.

## Aviso de dependência do Breakdance

O plugin exige Breakdance. Instale/ative o Breakdance e recarregue o admin.

## Locale errado (ex.: espanhol da Espanha em vez de LATAM)

Use `es_LA` (ou perfil `es_MX` / `es_419`, que aliasam para `es_LA`). Confira o language pack resolvido na tela de Languages.

## Hebraico / árabe com layout quebrado

1. Confirme o locale `he_IL` ou `ar`.
2. Limpe cache de CSS/JS.
3. Teste em site limpo (sem CSS custom conflitante).
4. Veja `includes/rtl-support.php` e `admin/assets/settings-tab.css`.

## Freemius / licença não aparece

1. Confirme `config/freemius.php` local (nunca no Git).
2. Confirme SDK em `vendor/freemius/`.
3. Veja [FREEMIUS-SETUP.md](./FREEMIUS-SETUP.md) e [FREEMIUS-TESTING.md](./FREEMIUS-TESTING.md).

## `.update.blb` inválido / versão errada

1. `python scripts/blb-manifest.py verify`
2. Confirme que `.blb-secret` local (ou `BLB_MANIFEST_SECRET`) é o mesmo usado na assinatura.
3. Detalhes: [BLB-MANIFEST.md](./BLB-MANIFEST.md).

## Placeholders quebrados (`% s`, `{ {`, etc.)

Rode o QA:

```powershell
cd wp-content/plugins/builder-languages-breakdance
python scripts/qa-placeholders.py --summary --all-supported
python scripts/fix-placeholder-spacing.py --locale LOCALE
python scripts/compile-mo.py
```

Gate de release: `pt_BR`, `pt_PT`, `it_IT` = **0** issues.
