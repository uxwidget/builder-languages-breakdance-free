# Builder Languages for Breakdance — Freemius: referência de testes

> Nome exibido: **Builder Languages for Breakdance**. Slug Freemius / páginas `breakdance-languages*`: mantidos.

Documento de referência para **retomar testes** de checkout, licença e ambiente local sem depender do painel confuso do Freemius.

Última atualização: **2026-07-13** — ZIP `0.1.13` empacotado (Deploy Freemius pendente de upload).

Relacionado: [FREEMIUS-SETUP.md](./FREEMIUS-SETUP.md) · [PRICING.md](../marketing/PRICING.md)

---

## IDs e links rápidos

| Item | Valor |
|------|-------|
| **Store ID** | `15920` |
| **Plugin ID** | `30587` |
| **Public Key** | `pk_e984dedde8057992b2e0735383e70` |
| **Plan ID** | `56028` (`Premium` / `premium`) |
| **Pricing IDs** | `74321` (1) · `74322` (5) · `74324` (20) · `74326` (50) |
| **Versão Released** | `0.1.12` (SemVer — sem prefixo `ux-`) |
| **Dashboard do produto** | https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/ |
| **Plans** | https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/plans/ |
| **Freemius Debug (dev)** | `http://sparklean-02.local/wp-admin/admin.php?page=freemius` |
| **Ativação (sales / blb01)** | `http://blb01.local/wp-admin/admin.php?page=breakdance-languages` |
| **Ativação (dev)** | `http://sparklean-02.local/wp-admin/admin.php?page=breakdance-languages` |

---

## Preços em produção (confirmados)

| Tier | Annual (USD) | Sites | Pricing ID |
|------|--------------|-------|------------|
| Personal (Single Site) | **39** | 1 | `74321` |
| Studio (5 Sites) | **79** | 5 | `74322` |
| Agency (20 Sites) | **179** | 20 | `74324` |
| Pro (50 Sites) | **299** | 50 | `74326` |

- **Monthly:** vazio (não usar no lançamento) ✓
- **Lifetime:** vazio (não usar no lançamento) ✓
- **Unlimited / Ilimitado:** **não oferecer** ✓
- **Is Hidden / White Labeled:** OFF em todos os tiers ✓

> IDs antigos (`73787` / `73788` / `73789`) obsoletos — substituídos em 2026-07-11.

### Estrutura no Freemius

**1 plano** (`Premium` / unique name `premium`) com **4 faixas de preço** (quota de sites):

| Campo | Valor |
|-------|-------|
| **Plan title** | `Premium` |
| **Unique name** | `premium` |
| **Description** | `All languages. Annual updates & support.` |

| Faixa | Sites | Annual | Pricing ID |
|-------|-------|--------|------------|
| Personal | 1 | $39 | `74321` |
| Studio | 5 | $79 | `74322` |
| Agency | 20 | $179 | `74324` |
| Pro | 50 | $299 | `74326` |

> Todos liberam o mesmo produto. Sem tier ilimitado.---

## Outras opções do plano

| Opção | Valor |
|-------|-------|
| **Unlimited localhost activations** | ON (recomendado) |
| **Trial** | No Trial (lançamento) |
| **Require Credit Card (trial)** | OFF |
| **Provide Support** | ON |
| **Email de suporte** | `support@uxwidget.com` |
| **Phone / Forum / Knowledge Base / PSM** | OFF |
| **Features → Block features after expiry** | ON |
| **Include all Free features** | Irrelevante (premium-only) |

O Freemius **salva automaticamente** — não há botão Save.

---

## Checkout: Sandbox vs Production

Cada tier tem **dois links** no botão **Checkout Link**:

| Ambiente | Uso |
|----------|-----|
| **Sandbox / Testing** | Testar compra com cartão de teste Freemius. **Não** publicar na LP. |
| **Production / Live** | Botões da LP e vendas reais. |

### Links Production (LP / marketplace → website)

| Tier | Pricing ID | Checkout |
|------|------------|----------|
| Personal 1 site ($39) | `74321` | https://checkout.freemius.com/plugin/30587/plan/56028/licenses/1/ |
| Studio 5 sites ($79) | `74322` | https://checkout.freemius.com/plugin/30587/plan/56028/licenses/5/ |
| Agency 20 sites ($179) | `74324` | https://checkout.freemius.com/plugin/30587/plan/56028/licenses/20/ |
| Pro 50 sites ($299) | `74326` | https://checkout.freemius.com/plugin/30587/plan/56028/licenses/50/ |

**Plan ID:** `56028`

```
Personal 1 site ($39) — ID 74321 — Production:
https://checkout.freemius.com/plugin/30587/plan/56028/licenses/1/

Studio 5 sites ($79) — ID 74322 — Production:
https://checkout.freemius.com/plugin/30587/plan/56028/licenses/5/

Agency 20 sites ($179) — ID 74324 — Production:
https://checkout.freemius.com/plugin/30587/plan/56028/licenses/20/

Pro 50 sites ($299) — ID 74326 — Production:
https://checkout.freemius.com/plugin/30587/plan/56028/licenses/50/
```

### Links Sandbox (QA interno — não publicar na LP)

No botão **Checkout Link** de cada faixa, escolha **Sandbox / Testing** e cole abaixo (ou use Prefill Form no checkout se o modo sandbox estiver ativo):

```
Personal 1 site ($39) — ID 74321 — Sandbox:
[PREENCHER]

Studio 5 sites ($79) — ID 74322 — Sandbox:
[PREENCHER]

Agency 20 sites ($179) — ID 74324 — Sandbox:
[PREENCHER]

Pro 50 sites ($299) — ID 74326 — Sandbox:
[PREENCHER]
```
> Cole os URLs reais aqui quando copiar do Freemius → Plans → Checkout Link.

---

## Teste sandbox realizado (2026-07-09)

**Status:** ✅ Checkout sandbox funcionou.

| Item | Detalhe |
|------|---------|
| **E-mail de teste** | `suitecomercial@gmail.com` |
| **Tipo** | Assinatura anual (ANNUAL) |
| **Ambiente** | SANDBOX |

### E-mails recebidos (confirmação)

1. `[SANDBOX] [INITIAL] W00t! You received a payment from suitecomercial@gmail.com`
2. `[SANDBOX] [SUBSCRIPTION] [ANNUAL] You have a new Breakdance Languages subscription`
3. `[SANDBOX] Thanks for upgrading`
4. `[SANDBOX] Your payment receipt for Breakdance Languages`

A **license key** vem no e-mail *Thanks for upgrading* ou *receipt*, ou em **Users** → e-mail do comprador no dashboard Freemius.

### Menu Licenses no dashboard

O Freemius **não mostra** o menu **Licenses** nem o botão **Create License** até existir **pelo menos uma transação** no produto (sandbox ou live).

| Antes do 1º pagamento sandbox | Depois do 1º pagamento sandbox |
|-------------------------------|--------------------------------|
| Menu **Licenses** ausente ou sem **Create License** | [Licenses](https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/licenses/) visível |
| Só bypass local ou checkout sandbox para gerar chave | License keys em **Users** → comprador → **Licenses** |

Para desenvolvimento local sem licença real, o **bypass** no `wp-config.php` continua sendo suficiente — não depende desse menu.

---

## Como testar ativação de licença no WordPress

**Pré-requisito para licença SANDBOX:** o `wp-config.php` **precisa** das 3 constantes Freemius. Sem elas, o SDK fala com a API **live** e a licença sandbox falha (ex.: *"Plugin does not exist"*).

```php
define( 'WP_FS__DEV_MODE', true );
define( 'WP_FS__SKIP_EMAIL_ACTIVATION', true );
define( 'WP_FS__breakdance-languages_SECRET_KEY', 'sk_...' ); // Secret Key do dashboard → Settings → Keys
```

> A secret key no `wp-config.php` **não** é a chave que se cola no formulário. Ela só habilita a API sandbox.

### Onde pegar a license key do cliente (campo de ativação)

| Origem | Caminho |
|--------|---------|
| E-mail sandbox | `[SANDBOX] Thanks for upgrading` → campo **License Key** |
| Dashboard | [Users](https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/users/) → `suitecomercial@gmail.com` → **Licenses** |
| Criar manualmente | [Licenses](https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/licenses/) → **Create License** |

A license key do cliente é **outra string**, gerada na compra ou em Create License — **não** é a Secret Key de Settings → Keys (mesmo que tenha 32 caracteres e caracteres especiais parecidos).

O site local `sparklean-02.local` usa **bypass de desenvolvimento** — a licença real pode não aparecer como "Ativa" enquanto o bypass estiver ativo.

### Opção A — Testar licença real (recomendado para validar fluxo)

1. Comente **temporariamente** no `wp-config.php`:
   ```php
   // define( 'WP_FS__DEV_MODE', true );
   // define( 'WP_FS__SKIP_EMAIL_ACTIVATION', true );
   // define( 'WP_FS__breakdance-languages_SECRET_KEY', 'sk_...' );
   ```
2. **Freemius Debug** → *Delete All Accounts* (se houver opt-in preso)
3. Desative e reative o plugin **Builder Languages for Breakdance**
4. Abra `admin.php?page=breakdance-languages`
5. Cole a **license key** (sandbox ou production)
6. Confirme em **Breakdance → Settings → Languages** → **Licença = Ativa**
7. Reative as constantes no `wp-config.php` para voltar ao modo dev

### Opção B — Continuar só com bypass local (desenvolvimento)

Com as 3 constantes no `wp-config.php`, traduções funcionam sem licença real. Painel mostra **Modo de desenvolvimento**.

---

## Modo desenvolvimento local (`wp-config.php`)

```php
define( 'WP_FS__DEV_MODE', true );
define( 'WP_FS__SKIP_EMAIL_ACTIVATION', true );
define( 'WP_FS__breakdance-languages_SECRET_KEY', 'sk_...' ); // só local, nunca no ZIP
```

| Constante | Função |
|-----------|--------|
| `WP_FS__DEV_MODE` | Modo dev + menu Freemius Debug |
| `WP_FS__SKIP_EMAIL_ACTIVATION` | Pula confirmação por e-mail no opt-in |
| `WP_FS__breakdance-languages_SECRET_KEY` | Secret key do dashboard (Settings → Keys) |

**Logs `[freemius]` / builder travando:** com `WP_FS__DEV_MODE` no `wp-config.php`, o Freemius em modo debug sobrecarrega o iframe do builder. O plugin agora:

- **Não carrega** o Freemius no frontend nem no `admin-ajax.php` (requisições do builder) em `*.local` com bypass ativo
- **Carrega** o Freemius só em telas wp-admin (licença, sandbox, Freemius Debug)
- **Silencia** `WP_FS__DEBUG_SDK` em todo o ambiente local com bypass

Para silenciar manualmente no admin também:

```php
define( 'WP_FS__DEBUG_SDK', false );
```

> Em PHP, ler a secret key com `constant('WP_FS__breakdance-languages_SECRET_KEY')` — o hífen no nome quebra se acessar sem `constant()`.

---

## Cupom de lançamento (quando for ao ar)

| Campo | Valor |
|-------|-------|
| **Código** | `BREAKDANCEFIRST` |
| **Desconto** | 30% |
| **Preço efetivo** | ~$27 / ~$55 / ~$125 / ~$209 (30% off em $39/$79/$179/$299) |

Criar no Freemius → **Coupons** no dia do lançamento no marketplace Breakdance.

---

## Marketplace Breakdance

- Plugin **aprovado** no marketplace oficial Breakdance (2026-07).
- Preços no marketplace devem bater com Freemius: **$39 / $79 / $179 / $299** (1 / 5 / 20 / 50 sites). Sem ilimitado.- LP: `https://uxwidget.com/builder-languages-breakdance` → botões com links **Production**.

---

## Reset rápido (quando algo travar)

1. `admin.php?page=freemius` → **Delete All Accounts**
2. Na tabela **Plugin Sites** → apagar `breakdance-languages` se existir install parcial
3. **Clear API Cache**
4. Desativar e reativar o plugin
5. Tentar opt-in ou ativação de licença de novo

---

## Erro: "Plugin does not exist" na ativação

**Não é limitação de localhost.** O Freemius trata `*.local` / `localhost` como ambiente de desenvolvimento e, por padrão, **não consome** a cota de sites da licença.

### Causa #1 (confirmada 2026-07-11): Public Key errada no plugin

| Onde | Valor |
|------|-------|
| **Correto (Settings → Keys)** | `pk_e984dedde8057992b2e0735383e70` |
| **Errado (causava o erro)** | `pk_0b7bc3212560eed788f2ac487921c` |

Com a Public Key desalinhada, a API responde *"Plugin does not exist"* **em local e em produção** (`uxwidget.com`).

### Causa #2: API live × license sandbox (ou o contrário)

| Modo do site (`wp-config`) | License que funciona |
|----------------------------|----------------------|
| `WP_FS__DEV_MODE` + product secret | Compra cartão **4242** / e-mail `[SANDBOX]` |
| Constantes Freemius **comentadas** | **Create License** (dashboard live) ou compra Production |

### Duas keys `sk_…` diferentes

No Freemius moderno, **license key e product secret** podem começar com `sk_`. São strings **diferentes**:

| Tipo | Onde ver | Uso |
|------|----------|-----|
| **Product Secret Key** | Settings → Keys | só no `wp-config` (sandbox) |
| **License Key** | Users → Licenses / e-mail / portal | só no campo Activate License |

Comparar os 4 primeiros e 4 últimos caracteres. Se forem iguais e a ativação falhar, o problema é ambiente (tabela acima) ou Public Key.

### Reset rápido

1. Com `DEV_MODE` ativo: **Freemius Debug** → Delete All Accounts + Clear API Cache  
2. Sem `DEV_MODE`: desative/reative o plugin; se travar, apague a option `fs_accounts` no banco  
3. Ative de novo com a license do **mesmo** ambiente da tabela

### Onde pegar a license key correta (sandbox)

1. Abra o e-mail **`[SANDBOX] Thanks for upgrading`**
2. Ou: [Dashboard → Users](https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/users/) → comprador → aba **Licenses**

### blb01 vs sparklean-02

| Site | Papel | Freemius |
|------|-------|----------|
| `blb01.local` | Sales / packaging | Teste live (constantes comentadas) ou sandbox |
| `sparklean-02.local` | Dev / i18n | Bypass `DEV_MODE` + secret; Public Key igual à produção |

---

## Checklist antes de abrir vendas reais

- [x] Links **Production** copiados e colados neste doc
- [ ] LP com botões apontando para Production
- [ ] Cupom `BREAKDANCEFIRST` criado (opcional no lançamento)
- [x] Deploy do ZIP premium `0.1.12` no Freemius (Released; build antiga removida)
- [x] Teste de ativação live no **blb01** (Licença Ativa / Produção)
- [ ] Remover constantes Freemius do `wp-config.php` em produção (uxwidget / clientes)

---

## Cartões de teste Freemius (sandbox)

Documentação: https://freemius.com/help/documentation/checkout/integration/testing/

Use os cartões e contas PayPal de teste indicados na documentação oficial do Freemius ao abrir um link **Sandbox / Testing**.
