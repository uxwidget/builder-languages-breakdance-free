# Builder Languages for Breakdance — Configuração Freemius

Guia passo a passo para o produto **Builder Languages for Breakdance** no Freemius.

> **Nome vs slug:** o nome exibido é *Builder Languages for Breakdance*. O **slug Freemius**, text domain e constantes `WP_FS__breakdance-languages_*` permanecem `breakdance-languages` (não renomear sem migração). Pasta do plugin: `builder-languages-breakdance/`.

- **Store ID:** `15920`
- **Plugin ID:** `30587`
- **Dashboard:** [Plugin Settings](https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/settings/information/)

Documentação oficial: [Integration with SDK](https://freemius.com/help/documentation/wordpress/integration-with-sdk/)

> **Testes e checkout:** ver também [FREEMIUS-TESTING.md](./FREEMIUS-TESTING.md) (sandbox validado, pricing IDs, links, reset).

---

## 1. Settings → Information

Preencha os dados do produto:

| Campo | Valor sugerido |
|-------|----------------|
| **Title** | Builder Languages for Breakdance |
| **Slug** | `breakdance-languages` (interno — não alterar) |
| **Short description** | Professional language packs for Breakdance Builder, admin, and first-party elements. |
| **Long description** | Translate the Breakdance Builder interface, Breakdance admin screens, and first-party elements in 8 languages (Portuguese Brazil, Portuguese, French, German, Spanish, Arabic, Japanese, and English International), with American English (`en_US`) as the default baseline — without editing Breakdance core files. |
| **Plugin URI** | `https://uxwidget.com/builder-languages-breakdance` |
| **Author** | UX Widget |
| **Author URI** | `https://uxwidget.com` |
| **Requires at least** | 6.0 |
| **Requires PHP** | 7.4 |
| **Text Domain** | `breakdance-languages` |

**Modelo de produto:** Premium-only (sem versão free permanente).

---

## 2. Plans & Pricing

Crie **3 planos anuais** (sem lifetime no lançamento):

| Plano | Preço | Sites | Nome interno (slug) |
|-------|-------|-------|---------------------|
| Single Site (Personal) | USD 49/ano | 1 | `personal` |
| Business | USD 99/ano | 5 | `business` |
| Agency | USD 199/ano | Ilimitado | `agency` |

Para cada plano, marque:

- **Annual subscription** — ativado
- **Lifetime** — desativado (no lançamento)
- **Updates & support** — incluídos no período da assinatura

Opcional depois do lançamento: trial de 7 dias no plano Business.

---

## 3. SDK Integration

Abra: **SDK Integration** no menu do plugin no Freemius.

Preencha para bater com o código deste repositório:

| Campo Freemius | Valor |
|----------------|-------|
| **Product ID** | `30587` |
| **Plugin slug** | `breakdance-languages` |
| **Main plugin file** | `builder-languages-breakdance.php` |
| **Function name** | `breakdance_languages_fs` |
| **Premium-only** | Yes |
| **Has paid plans** | Yes |
| **Parent menu slug** | `breakdance` |
| **Submenu slug** | `breakdance-languages` (conta em `breakdance-languages-account`) |

Copie da página:

1. **Public Key** (`pk_...`) → colar em `config/freemius.php`
2. Confira se o snippet gerado usa o mesmo `slug` e `id`

---

## 4. Instalar o SDK no plugin

O SDK ainda **não está** em `vendor/freemius/`. Escolha uma opção:

### Opção A — Manual (recomendado)

1. Baixe: https://github.com/Freemius/wordpress-sdk/archive/master.zip
2. Extraia e renomeie a pasta para `freemius`
3. Copie para: `wp-content/plugins/builder-languages-breakdance/vendor/freemius/`
4. Confirme que existe: `vendor/freemius/start.php`

### Opção B — Composer

```bash
cd wp-content/plugins/builder-languages-breakdance
composer require freemius/wordpress-sdk
```

---

## 5. Credenciais locais

1. Copie `config/freemius.config.example.php` → `config/freemius.php`
2. Cole o **Plugin ID** e **Public Key** do dashboard:

```php
define('BREAKDANCE_LANGUAGES_FREEMIUS_ID', '30587');
define('BREAKDANCE_LANGUAGES_FREEMIUS_PUBLIC_KEY', 'pk_COLE_AQUI');
```

> `config/freemius.php` não vai para o ZIP público (está no `.distignore`). Inclua só no build comercial.

---

## 6. Modo de desenvolvimento (teste local)

Documentação oficial: [Testing your product](https://freemius.com/help/documentation/wordpress-sdk/testing/#setting-freemius-into-development-mode)

Em `wp-config.php` do site Local (**antes** do comentário “pare de editar”):

```php
/* Freemius — modo desenvolvimento (Builder Languages for Breakdance) */
define( 'WP_FS__DEV_MODE', true );
define( 'WP_FS__SKIP_EMAIL_ACTIVATION', true );
define( 'WP_FS__breakdance-languages_SECRET_KEY', 'sk_COLE_AQUI' );
```

| Constante | Função |
|-----------|--------|
| `WP_FS__DEV_MODE` | Ativa modo dev + menu **Freemius Debug** no admin |
| `WP_FS__breakdance-languages_SECRET_KEY` | Secret key do dashboard (Settings → Keys). Necessária para sandbox e para pular confirmação por e-mail |
| `WP_FS__SKIP_EMAIL_ACTIVATION` | Só funciona **junto com** a secret key no `wp-config.php` |

> A secret key **não** libera o plugin automaticamente. Ela habilita checkout sandbox e pula confirmação por e-mail no opt-in.

### 6.1 E-mail real (obrigatório)

O Freemius **rejeita** e-mails locais (`admin@localhost`, `@test.`, `@dev.`, etc.).

1. Vá em **Usuários → Perfil**
2. Use um e-mail real (Gmail, etc.)
3. Salve o perfil

Sem isso, o dashboard mostra: *“Could not verify integration — No opt-in confirmation has been received”*.

### 6.2 Obter licença de teste

**Opção A — Checkout sandbox (recomendado para validar o fluxo real)**

Com `WP_FS__DEV_MODE` + secret key no `wp-config.php`, abra a página de preços/checkout do plugin e compre em modo sandbox (cartão de teste do Freemius). A license key é gerada automaticamente.

**Opção B — Create License no dashboard**

Só aparece em: [Plugin 30587 → Licenses](https://dashboard.freemius.com/#!/live/stores/15920/plugins/30587/licenses/) (não é menu global).

> **Comportamento confirmado (2026-07):** o menu **Licenses** e o botão **Create License** só ficam disponíveis **depois da primeira transação** — checkout sandbox com cartão de teste já conta. Antes disso, o item não aparece no dashboard (mesmo com planos configurados). Use a Opção A (checkout sandbox) ou o bypass local abaixo.

**Opção C — Bypass local (só desenvolvimento)**

Com as 3 constantes no `wp-config.php`, o plugin libera traduções em `*.local` sem licença real. O painel mostra **Modo de desenvolvimento** em vez de **Ativa**.

### 6.3 Freemius Debug (resetar estado preso)

Menu **Freemius Debug** ou: `admin.php?page=freemius`

1. **Delete All Accounts** — limpa opt-ins falhos / estado corrompido
2. Na tabela **Plugin Sites**, localize `breakdance-languages` → **Delete** se houver install parcial
3. **Clear API Cache** → tente o opt-in de novo em `admin.php?page=breakdance-languages`

O Debug **não ativa licença** sozinho; serve para resetar e inspecionar dados.

### 6.4 Ativar no site local

1. Confirme as 3 constantes no `wp-config.php`
2. **Desative e reative** o plugin Builder Languages for Breakdance
3. Abra: `admin.php?page=breakdance-languages`  
   (ou **Breakdance → Settings → Languages → Gerenciar licença**)
4. Complete o opt-in com e-mail real + license key
5. Se ficar preso: menu **Freemius Debug** → apague dados do plugin → tente de novo
6. Confira em **Breakdance → Settings → Languages**:
   - **Licença** = **Ativa**
   - Seletor de idioma habilitado

### 6.5 Verificar integração no dashboard Freemius

A verificação em **SDK Integration** só passa **depois** que o opt-in no site local for concluído com sucesso. As constantes do `wp-config.php` não substituem esse passo.

---

## 7. Checkout & Payments

No Freemius:

- Ative **Sandbox Payments** para testes
- Configure moeda (USD ou BRL, conforme sua LP)
- Defina política de reembolso (ver `docs/REFUND-POLICY.md`)
- Personalize e-mail pós-compra com link de download + instruções de instalação

---

## 8. Deployment

Quando a integração estiver testada:

1. **Deploy** a versão no Freemius (menu Deployment)
2. Faça upload do ZIP premium
3. Teste download + ativação de licença em site limpo
4. Confirme **auto-updates** funcionando

---

## 9. O que o plugin já faz (código)

| Recurso | Status |
|---------|--------|
| Init early do SDK (`includes/freemius-init.php`) | Pronto |
| `breakdance_languages_is_licensed()` | Pronto |
| Bloqueio de traduções sem licença | Pronto |
| Painel Licença na aba Languages | Pronto |
| Submenu Conta em Breakdance | Configurado no SDK |
| Modo dev sem Freemius | Tudo liberado |

---

## 10. Checklist final

### Local (sparklean-02.local)

- [x] SDK em `vendor/freemius/start.php`
- [x] `config/freemius.php` com ID `30587` + public key
- [x] 3 constantes Freemius no `wp-config.php`
- [x] Bypass local ativo (`*.local` + secret key)
- [ ] Seletor de idioma testado na aba Languages
- [ ] Traduções validadas no Breakdance Builder (2–3 idiomas)

### Freemius (antes do lançamento)

- [ ] 3 planos criados (Personal / Business / Agency)
- [ ] SDK Integration preenchido no dashboard
- [ ] Checkout sandbox testado (Plans → Sandbox Link)
- [ ] Deploy da primeira versão premium
- [ ] LP com checkout publicada

> **Nota:** Licença manual no dashboard só aparece após a primeira venda (ou compra sandbox). Para desenvolvimento local, o bypass no `wp-config.php` é suficiente.

---

## Precisa de ajuda?

Envie a **Public Key** (`pk_...`) gerada na página SDK Integration para completarmos o `config/freemius.php` local.
