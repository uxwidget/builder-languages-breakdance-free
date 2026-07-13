# Ambientes — desenvolvimento vs versão de venda

## Regra

**Não ative duas cópias (nem duas “versões”) deste plugin no mesmo WordPress.**

Slug Freemius / text domain interno: `breakdance-languages`. Pasta: `builder-languages-breakdance`. Se houver outro ZIP com o mesmo produto (dev + build comercial, ou pasta duplicada), o WordPress conflita: mesma função PHP, mesmo text domain, risco de licença Freemius errada e updates quebrados.

| Ambiente | Site Local (exemplo) | Objetivo |
| --- | --- | --- |
| Desenvolvimento / beta | `sparklean-02` (ou `sparklean-dev`) | Código do Git, QA de traduções, painel Languages |
| Versão de venda | **site WordPress novo e separado** (`blb01`) | Build comercial + Freemius (checkout, licença, updates pagos) |

**Conclusão:** a versão de venda precisa de uma **instalação WordPress nova**, só para ela. Não misture com o site onde você desenvolve o plugin.

### Não confundir os dois Locals

No `wp-config.php` de cada site:

```php
// blb01 (embalagem / Freemius comercial)
define( 'BREAKDANCE_LANGUAGES_CHANNEL', 'sales' );

// sparklean (traduções / Git)
define( 'BREAKDANCE_LANGUAGES_CHANNEL', 'dev' );
```

Na admin bar local aparece **BLB · Sales · ux-x.x.x** ou **BLB · Dev · ux-x.x.x**.  
Não copie pasta do plugin entre sites sem decidir qual é a fonte da mudança. ZIP comercial sai só do site **sales** via `pack-release.py`.

---

## Site de desenvolvimento

- Fonte: repositório Git (symlink ou cópia da pasta do plugin).
- Freemius: pode ficar desligado ou em modo `WP_FS__DEV_MODE` (ver [DEV-SETUP.md](./DEV-SETUP.md)).
- Secrets locais: `.blb-secret`, `config/freemius.php` — **nunca** no Git.

## Site de venda (comercial)

1. Criar site novo no Local (nome sugerido: `blb-venda` ou `builder-languages-store`).
2. Instalar só o necessário: WordPress + Breakdance + **um** ZIP gerado por `python scripts/pack-release.py` (ver [PACK-RELEASE.md](./PACK-RELEASE.md)).
3. Usar `config/freemius.php` / constantes do **produto Freemius de produção** (via `--freemius-config` no pack, ou no servidor — nunca misturar secret de dev no mesmo site).
4. Testar checkout, ativação de licença e update — checklist em [FREEMIUS-TESTING.md](./FREEMIUS-TESTING.md).

Não importe o banco do site de cliente cheio. Ambiente mínimo = resultado confiável.

---

## O que não fazer

- Ativar `builder-languages-breakdance` e um ZIP antigo `breakdance-languages` no mesmo WP.
- Symlink do repo de dev **e** ZIP comercial na mesma pasta `plugins/`.
- Testar licença de produção no mesmo site onde você edita código o dia todo (polui estado Freemius / opções).

---

## Documentação das mudanças

Toda alteração de produto continua registrada no repositório (mesmo que o teste de venda seja em outro WP):

- [CHANGELOG.md](./CHANGELOG.md) — histórico oficial por versão
- [ATUALIZACOES.md](./ATUALIZACOES.md) — como registrar cada bump
- Repo privado: `marceloadias/builder-languages-breakdance`
