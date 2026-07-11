# Builder Languages for Breakdance — Ambiente de desenvolvimento limpo

Guia para criar um **WordPress mínimo** no Local WP e ativar plugins **um a um**, isolando bugs do plugin em desenvolvimento.

Relacionado: [AMBIENTES.md](./AMBIENTES.md) · [ATUALIZACOES.md](./ATUALIZACOES.md) · [INSTALLATION.md](./INSTALLATION.md) · [TROUBLESHOOTING.md](./TROUBLESHOOTING.md) · [FREEMIUS-TESTING.md](./FREEMIUS-TESTING.md)

Última atualização: **2026-07-11**

---

## Dev vs versão de venda

Este guia cobre o **site de desenvolvimento**. A versão de venda (Freemius / checkout / updates pagos) exige um **WordPress novo e separado** — não ative duas cópias do plugin no mesmo WP. Detalhes: [AMBIENTES.md](./AMBIENTES.md).

Histórico de mudanças: [CHANGELOG.md](./CHANGELOG.md) · processo: [ATUALIZACOES.md](./ATUALIZACOES.md).

---

## Por que um site limpo?

Sites como `sparklean-02` costumam ter cache, SEO, importações, muitos plugins e templates. Isso dificulta saber se um erro vem do **Builder Languages for Breakdance** ou de outro componente.

| Sintoma confuso | Causa comum em site “cheio” |
|-----------------|----------------------------|
| Sessão expirada no builder | Nonce do Breakdance + aba aberta há horas |
| `admin-ajax.php` 500 | Timeout ao baixar pacote de idioma + outro plugin |
| Tradução parcial | Cache (WP Rocket) ou domínio JSON errado |
| Perfil em “Padrão do site” | Pacote WP core ausente + filtros de terceiros |

**Regra:** um site só para dev do plugin; outro site pode continuar como referência do cliente (`sparklean-01`, `sparklean-02`).

---

## 1. Criar site novo no Local

1. Abra **Local** → **+ Create a new site**
2. Nome sugerido: `sparklean-dev`
3. Ambiente: **Preferred** (PHP 8.x, nginx/Apache padrão)
4. Usuário/senha: anote (ex. `admin` / `admin`)
5. **Não** importe conteúdo, Duplicator, ou backup de outro site

Após criar, anote a URL local (ex. `http://sparklean-dev.local`).

---

## 2. Configurar `wp-config.php`

No site novo, adicione **antes** de `require_once ABSPATH . 'wp-settings.php';`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', '0' );

/* Freemius — modo desenvolvimento (Builder Languages for Breakdance) */
define( 'WP_FS__DEV_MODE', true );
define( 'WP_FS__SKIP_EMAIL_ACTIVATION', true );
define( 'WP_FS__breakdance-languages_SECRET_KEY', 'sk_...' ); // Secret Key do dashboard Freemius
```

**Não** ative cache agressivo, `DISALLOW_FILE_MODS`, ou plugins de otimização nesta fase.

Log de erros: `wp-content/debug.log`

---

## 3. Instalar o plugin em desenvolvimento

### Opção A — Symlink (recomendado)

Aponte o plugin para a pasta do repositório para editar sem copiar:

```powershell
# PowerShell (admin) — ajuste os caminhos
New-Item -ItemType SymbolicLink `
  -Path "C:\Users\marce\Local Sites\sparklean-dev\app\public\wp-content\plugins\builder-languages-breakdance" `
  -Target "C:\Users\marce\Local Sites\sparklean-02\app\public\wp-content\plugins\builder-languages-breakdance"
```

### Opção B — Cópia manual

Copie a pasta `builder-languages-breakdance` para `wp-content/plugins/` do site novo sempre que quiser testar uma versão fixa.

---

## 4. Ordem de ativação dos plugins

Ative **nesta ordem**. Rode o checklist após cada etapa antes de passar à próxima.

### Etapa 0 — WordPress nu

- [ ] Login em `/wp-admin/` funciona
- [ ] Apenas plugins padrão (Akismet, etc.) — pode desativar todos
- [ ] Nenhuma página além de “Hello world” / “Sample Page” (pode excluir depois)
- [ ] `wp-content/debug.log` existe e está vazio (ou só avisos irrelevantes)

### Etapa 1 — Breakdance

1. Instale e ative **Breakdance** (mesma versão que você usa em produção/staging)
2. Complete o onboarding mínimo se o Breakdance pedir

**Checklist**

- [ ] Menu **Breakdance** aparece no admin
- [ ] Criar uma página de teste e abrir o **builder** (`?breakdance=open`)
- [ ] Builder carrega sem erro fatal
- [ ] `debug.log` sem erros novos críticos

### Etapa 2 — Builder Languages for Breakdance

1. Ative **Builder Languages for Breakdance**
2. Vá em **Breakdance → Idiomas** (`admin.php?page=breakdance-languages-settings`)

**Checklist — painel Idiomas**

- [ ] Página carrega sem tela branca / parse error
- [ ] Select de idioma + botão **Atualizar** na mesma linha
- [ ] Trocar idioma (ex. `pt_BR`) → barra de progresso com **%**
- [ ] Mensagem de sucesso + botão Atualizar visível
- [ ] Bloco de perfil WordPress mostra idioma atual
- [ ] Licença: **Development mode** (com `WP_FS__DEV_MODE`)

**Checklist — pacote WordPress core**

- [ ] Escolher idioma sem pacote WP (ex. `de_DE`) → aviso de pacote faltando
- [ ] **Instalar idioma do WordPress** → progresso com % → sucesso ou erro **claro**
- [ ] Após instalar: `wp-content/languages/de_DE.l10n.php` (ou `.mo`) existe
- [ ] **Usuários → Perfil → Idioma** lista o idioma corretamente

**Checklist — builder**

- [ ] Abrir builder em nova aba
- [ ] Após salvar idioma no painel: recarregar builder (`Ctrl+Shift+R`)
- [ ] Guias dos elementos (Typography, Size, etc.) no idioma escolhido
- [ ] Painel **Adicionar** — nomes de elementos traduzidos
- [ ] Sem popup “Sua sessão expirou” logo após salvar idioma (se aparecer, recarregue o builder)

### Etapa 3 — Freemius (opcional, quando testar licença)

Só quando for validar checkout/ativação real:

- [ ] Seguir [FREEMIUS-TESTING.md](./FREEMIUS-TESTING.md)
- [ ] Testar `admin.php?page=breakdance-languages` (ativação)
- [ ] Testar sandbox checkout sem misturar com cache ou outros plugins

### Etapa 4 — Plugins de integração (um por vez)

Adicione **um plugin**, teste o checklist da Etapa 2 + builder, depois o próximo:

| Ordem sugerida | Plugin | O que validar |
|----------------|--------|----------------|
| 4a | WooCommerce | Elementos WooCommerce, strings `woocommerce` |
| 4b | Plugin de cache | Traduções após limpar cache |
| 4c | SEO (Rank Math, etc.) | Admin não quebra; builder intacto |
| 4d | Admin Menu Editor | Menu Breakdance → Idiomas ainda acessível |

Se algo quebrar **só após** ativar o plugin X, o conflito é com X — anote no issue.

---

## 5. URLs úteis (ajuste o domínio)

Substitua `sparklean-dev.local` pelo seu site Local:

| Tela | URL |
|------|-----|
| Idiomas | `/wp-admin/admin.php?page=breakdance-languages-settings` |
| Ativação Freemius | `/wp-admin/admin.php?page=breakdance-languages` |
| Perfil WP | `/wp-admin/profile.php` |
| Atualizações → Traduções | `/wp-admin/update-core.php` |
| Debug Freemius | `/wp-admin/admin.php?page=freemius` |
| Log PHP | `wp-content/debug.log` (via editor ou IDE) |

---

## 6. O que evitar no site de dev

Até o plugin estar estável, **não** instale:

- WP Rocket / cache de página agressivo
- Duplicator / migrações
- Importadores de demo (Astra, etc.)
- Admin Menu Editor (esconde menus e causa 403)
- Múltiplos plugins de SEO ao mesmo tempo

Páginas importadas, templates Breakdance de cliente e formulários complexos podem entrar **depois**, em um site de staging separado.

---

## 7. Fluxo de trabalho diário

1. Editar código em `builder-languages-breakdance/`
2. Recarregar painel **Idiomas** com `Ctrl+F5` (JS/CSS versionados por `BREAKDANCE_LANGUAGES_VERSION`)
3. Testar mudança no builder com hard refresh
4. Se AJAX falhar: abrir `debug.log` + aba Network → `admin-ajax.php`
5. Commit só quando Etapa 2 passar no site limpo

### Bump de versão para cache-bust

Ao alterar `admin/assets/settings-tab.js` ou `.css`, incremente a versão em `builder-languages-breakdance.php` (`BREAKDANCE_LANGUAGES_VERSION`) para o navegador não servir arquivo antigo.

---

## 8. Sites neste projeto

| Site | Uso recomendado |
|------|-----------------|
| `sparklean-dev` (novo) | Desenvolvimento do plugin, checklist acima |
| `sparklean-02` | Staging / testes com mais plugins |
| `sparklean-01` | Referência antiga — não usar como base de dev |

---

## 9. Problemas comuns no ambiente limpo

| Problema | Solução |
|----------|---------|
| Pacote de idioma não baixa | Conexão com wordpress.org; instalar em **Painel → Atualizações → Traduções** |
| “Sessão expirou” no builder | Recarregar aba do builder; manter painel Idiomas e builder sincronizados |
| Idioma salvo mas builder em inglês | Clicar **Atualizar** no painel Idiomas ou `Ctrl+Shift+R` no builder |
| Menu Idiomas sumiu | Não usar `remove_submenu_page` manual; ver [TROUBLESHOOTING.md](./TROUBLESHOOTING.md) |
| Licença inativa em dev | Confirmar `WP_FS__DEV_MODE` e secret key no `wp-config.php` |

---

## 10. Próximos passos

- [ ] Criar site `sparklean-dev` no Local
- [ ] Aplicar `wp-config` de debug + Freemius dev
- [ ] Symlink do plugin
- [ ] Completar Etapas 0 → 2
- [ ] Só então reintroduzir plugins do site real, um a um

Quando a Etapa 2 estiver estável, use `sparklean-02` para testes de integração mais pesados sem poluir o ambiente mínimo.

---

## 11. Validação de catálogos (auditoria / IAs)

Checklist reproduzível para revisores humanos ou IAs confirmarem que **nenhum locale existente quebrou** e que um locale novo (ex. `it_IT`) está completo.

Relacionado: [COMPATIBILITY.md](./COMPATIBILITY.md) · [AVALIACAO.md](./AVALIACAO.md)

### Locales suportados (10)

| Código | Papel |
|--------|--------|
  | 17 idiomas de produto + `en_US`/`en_GB` | ver `config/supported-locales.json` |
| `en_US` | Baseline (inglês americano) |
| `en_GB` | Inglês internacional |

Fonte de verdade no código: `includes/locale.php` → `breakdance_languages_supported_locale_codes()`.

### Pré-requisitos

- Python 3.8+
- Dependência: `polib` (`pip install polib`)
- Terminal na raiz do plugin: `wp-content/plugins/builder-languages-breakdance/`

### Comandos obrigatórios

```powershell
cd wp-content/plugins/builder-languages-breakdance

# Validação consolidada (recomendado)
python scripts/validate-all.py
python scripts/compare-locale-coverage.py   # paridade vs pt_BR

# Roteiro completo de paridade: docs/TRANSLATION-ROADMAP.md

# Ou passo a passo:
# 1. Verificar PO + JSON de todos os locales
python scripts/verify-catalogues.py

# 2. Recompilar .mo após alterar .po
python scripts/compile-mo.py

# 3. QA de placeholders (gate: pt_BR/pt_PT/it_IT = 0)
python scripts/qa-placeholders.py --summary --all-supported
```

**Saída esperada de `verify-catalogues.py`:** uma linha `OK` por coluna para **cada** locale, terminando com:

```text
All locales have breakdance, breakdance-builder, and breakdance-elements catalogues.
```

Exit code `0`. Qualquer `MISSING`, `MISMATCH` ou exit code `1` = falha.

### QA de placeholders (qualidade MT)

| Locale | Gate de release? | Expectativa |
|--------|------------------|-------------|
| `pt_BR`, `pt_PT` | **Sim** | 0 suspeitas |
| `it_IT` | **Sim** | 0 suspeitas |
| `fr_FR`, `de_DE`, `es_ES`, `ar`, `ja_JP` | Não (beta) | Rastrear tendência; corrigir `%sPro`/HTML com `fix-placeholder-spacing.py` |
| `en_US`, `en_GB` | N/A | Baseline; ~2 falsos positivos aceitáveis |

Correção em massa após machine translation:

```powershell
python scripts/fix-placeholder-spacing.py
python scripts/generate-locale.py --target fr_FR --json-only  # repetir por locale alterado
python scripts/compile-mo.py
```

### Divisores de forma (Shape Dividers)

Labels como `Angle1` vêm do nome do arquivo SVG no Breakdance (sem gettext). O plugin traduz **somente o texto exibido** via filtro `breakdance_shape_dividers`; o valor SVG salvo no builder não muda.

```powershell
python scripts/verify-shape-dividers.py
```

Mapas por locale: `config/shape-divider-labels.json` (hoje: `pt_BR` completo).

### Arquivos exigidos por locale

Para cada código em `scripts/verify-catalogues.py` (`LOCALES`):

| Arquivo | Domínio |
|---------|---------|
| `languages/breakdance-{locale}.po` | Admin Breakdance |
| `languages/breakdance-builder-{locale}.po` | Builder Vue |
| `languages/breakdance-elements-{locale}.po` | Elementos |
| `languages/breakdance-{locale}.json` | JED builder (espelha builder PO) |
| `languages/breakdance-elements-{locale}.json` | JED elements |

Além disso, o plugin em si publica `languages/breakdance-languages-{locale}.po` (e `.mo`) para strings do painel **Breakdance → Idiomas**.

### Registro no PHP (auditoria estática)

Confirmar que o locale aparece em:

| Arquivo | O que checar |
|---------|----------------|
| `includes/locale.php` | `supported_locale_codes()` e labels |
| `includes/ui-strings.php` | Catálogo do painel + `locale_labels_catalog` |
| `includes/design-library.php` | Mapas Samba/UI quando aplicável |
| `scripts/verify-catalogues.py` | Array `LOCALES` |
| `scripts/generate-locale.py` | `LOCALE_META` + `TRANSLATE_TARGETS` (se gerado via script) |
| `docs/COMPATIBILITY.md` | Lista de locales suportados |

**Runtime overrides** (`includes/editor-overrides.php`): apenas `pt_BR`, `pt_PT`, `ja_JP`. Outros idiomas dependem de PO/JSON — isso é intencional, não é lacuna de catálogo.

### Gerar um locale novo (ex. após adicionar idioma)

```powershell
python -u scripts/generate-locale.py --source en_US --target it_IT --translate
python scripts/compile-mo.py
python scripts/verify-catalogues.py
```

A geração MT pode levar ~60–70 min (milhares de entradas). Se o processo for interrompido, **rerode o comando** — não confie em PO parciais.

### Teste manual pós-validação

1. **Breakdance → Idiomas** → selecionar o locale → **Atualizar**
2. Hard refresh no builder (`Ctrl+Shift+R`)
3. Confirmar guias (Typography, Size, etc.) e painel **Adicionar**
4. Trocar de volta para um locale já existente (ex. `pt_BR`) e repetir — garante que nada regrediu

### Limitações conhecidas (não falham a auditoria)

- Botão **Add to page** dentro do iframe remoto da Design Library pode permanecer em inglês (conteúdo do design set, não UI do Breakdance).
- Strings hardcoded fora dos catálogos podem exigir patch em `editor-overrides.php` (hoje só pt-BR, pt-PT, ja-JP).

### Lint PHP (opcional)

Na pasta do plugin, com PHP no PATH:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

Nenhuma linha `Parse error` deve aparecer.
