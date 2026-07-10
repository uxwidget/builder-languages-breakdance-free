# Builder Languages for Breakdance — Freemius: referência de testes

> Nome exibido: **Builder Languages for Breakdance**. Slug Freemius / páginas `breakdance-languages*`: mantidos.

Documento de referência para **retomar testes** de checkout, licença e ambiente local sem depender do painel confuso do Freemius.

Última atualização: **2026-07-09** — checkout sandbox validado com sucesso.

Relacionado: [FREEMIUS-SETUP.md](./FREEMIUS-SETUP.md) · [PRICING.md](../marketing/PRICING.md)

---

## IDs e links rápidos

| Item | Valor |
|------|-------|
| **Store ID** | `15920` |
| **Plugin ID** | `30587` |
| **Slug** | `breakdance-languages` |
| **Dashboard do produto** | https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/ |
| **Plans** | https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/plans/ |
| **Freemius Debug (local)** | `http://sparklean-02.local/wp-admin/admin.php?page=freemius` |
| **Ativação no WP** | `http://sparklean-02.local/wp-admin/admin.php?page=breakdance-languages` |
| **Aba Languages** | `http://sparklean-02.local/wp-admin/admin.php?page=breakdance_settings&tab=languages` |

---

## Preços em produção (confirmados)

| Tier | Annual (USD) | Sites | Pricing ID |
|------|--------------|-------|------------|
| Single Site | **49** | 1 | `73787` |
| 5 Sites | **99** | 5 | `73788` |
| Unlimited Sites | **199** | Ilimitado | `73789` |

- **Monthly:** vazio (não usar no lançamento)
- **Lifetime:** vazio (não usar no lançamento)
- **Is Hidden / White Labeled:** OFF em todos os tiers

### Estrutura no Freemius

Foi configurado **1 plano** com **3 faixas de preço** (não 3 planos separados):

| Campo do plano | Valor configurado |
|----------------|-------------------|
| **Title** | `Personal` |
| **Unique name** | `personal` |
| **Description** | `1 site. All languages. 1 year of updates and support.` |

> Todos os tiers liberam o mesmo produto (todas as traduções). A diferença é só o **número de ativações/sites**.

---

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

### Links Sandbox (preencher ao copiar do dashboard)

```
Single Site (73787) — Sandbox:
[PREENCHER]

5 Sites (73788) — Sandbox:
[PREENCHER]

Unlimited Sites (73789) — Sandbox:
[PREENCHER]
```

### Links Production (preencher ao copiar do dashboard)

```
Single Site (73787) — Production:
[PREENCHER]

5 Sites (73788) — Production:
[PREENCHER]

Unlimited Sites (73789) — Production:
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
| **Preço efetivo** | ~$34 / ~$69 / ~$139 |

Criar no Freemius → **Coupons** no dia do lançamento no marketplace Breakdance.

---

## Marketplace Breakdance

- Plugin **aprovado** no marketplace oficial Breakdance (2026-07).
- Preços no marketplace devem bater com Freemius: **$49 / $99 / $199**.
- LP: `https://uxwidget.com/builder-languages-breakdance` → botões com links **Production**.

---

## Reset rápido (quando algo travar)

1. `admin.php?page=freemius` → **Delete All Accounts**
2. Na tabela **Plugin Sites** → apagar `breakdance-languages` se existir install parcial
3. **Clear API Cache**
4. Desativar e reativar o plugin
5. Tentar opt-in ou ativação de licença de novo

---

## Erro: "Plugin does not exist" na ativação

**Causa mais comum:** colar a **Secret Key** (`sk_...`) no campo de license key.

| Tipo | Prefixo | Onde fica | Campo correto |
|------|---------|-----------|---------------|
| **Secret Key** (dev) | `sk_` | Freemius → Settings → Keys · `wp-config.php` | **Nunca** no formulário de ativação |
| **License Key** (cliente) | sem `sk_` | E-mail *Thanks for upgrading* · Dashboard → Users → Licenses | Campo "Enter your license key" |

A license key do cliente tem até **32 caracteres** e vem da compra sandbox/production.

### Onde pegar a license key correta (sandbox)

1. Abra o e-mail **`[SANDBOX] Thanks for upgrading`**
2. Ou: [Dashboard → Users](https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/users/) → `suitecomercial@gmail.com` → aba **Licenses**

### sparklean-01 vs sparklean-02

| Site | wp-config Freemius | Uso |
|------|-------------------|-----|
| `sparklean-02.local` | Constantes comentadas ou ativas (bypass) | Dev principal |
| `sparklean-01.local` | **Sem** constantes Freemius | Ideal para testar **ativação real** com license key |

Para desenvolver no `sparklean-01` sem licença, adicione as 3 constantes no `wp-config.php` (igual ao sparklean-02).

---

## Checklist antes de abrir vendas reais

- [ ] Links **Production** copiados e colados neste doc
- [ ] LP com 3 botões apontando para Production
- [ ] Cupom `BREAKDANCEFIRST` criado (opcional no lançamento)
- [ ] Deploy do ZIP premium no Freemius
- [ ] Teste de ativação com licença **production** em site limpo
- [ ] Remover constantes Freemius do `wp-config.php` em produção

---

## Cartões de teste Freemius (sandbox)

Documentação: https://freemius.com/help/documentation/checkout/integration/testing/

Use os cartões e contas PayPal de teste indicados na documentação oficial do Freemius ao abrir um link **Sandbox / Testing**.
