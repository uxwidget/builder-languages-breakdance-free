# Relatório de Atualizações — Builder Languages for Breakdance

**Data:** 10 de julho de 2026  
**Site de referência:** `sparklean-02.local`  
**Plugin:** `wp-content/plugins/builder-languages-breakdance`  
**Validação final:** `python scripts/validate-all.py` → **OVERALL: PASS**  
**Atualização:** 10 jul 2026 (noite) — MT dos 6 locales que estavam só estruturais  
**Pós-relatório:** rename de marca, manifesto `.update.blb`, repo GitHub privado, docs em pt-BR

---

## Resumo executivo

O plugin **Builder Languages for Breakdance** (slug Freemius interno `breakdance-languages`) passou de um conjunto focado em pt-BR/pt-PT para um produto com **17 idiomas** comercializáveis, cobertura profunda do **Form Builder**, suporte **RTL (Hebraico/Árabe)**, prioridade comercial para **Hindi** e **Hebraico** com catálogos MT completos, e infraestrutura centralizada de locales para Freemius/geolocalização.

Nenhuma alteração quebrou os locales de **release gate** (`pt_BR`, `pt_PT`, `it_IT` = 0 placeholders).

### Cobertura pós-MT (10 jul — 2ª onda)

| Locale | Método | Cobertura média |
|--------|--------|-----------------|
| `es_LA` | Cópia de `es_ES` (4.357 entradas) | **~95%** |
| `zh_CN` | MT Google (~4.595) | **~99%** |
| `ko_KR` | MT Google (~4.604) | **~99%** |
| `nl_NL` | MT Google (~4.604) | **~94%** |
| `ru_RU` | MT Google (~4.604) | **~99%** |
| `pl_PL` | MT Google (~4.604) | **~97%** |

Todos os 16 packs localizados estão agora **usáveis** (não mais catálogos vazios em inglês).

---

## 1. Form Builder (pt-BR + runtime)

### Problema
Labels do Form Builder (Actions After Submission, Store Submission, Email, CSRF, etc.) não passavam pelo gettext do core Breakdance.

### Solução (3 camadas)

| Camada | Arquivo | Função |
|--------|---------|--------|
| PHP controls | `includes/form-builder-i18n.php` | Filtro `breakdance_element_controls` (prioridade 100) traduz labels, placeholders, repeaters e HTML de alertas |
| Runtime JS | `includes/editor-overrides.php` | MutationObserver + patch AJAX/Pinia; regex `N selected` → `N selecionado(s)` só para `pt_*` |
| Catálogo PO | `languages/breakdance-elements-pt_BR.po` | +27 entradas Form Builder / Archive Title / Post Title |

### Strings cobertas (exemplos)
- Ações após envio, Armazenar envio, E-mails, Adicionar campo  
- Proteção CSRF, reCAPTCHA, honeypot  
- Título do arquivo, Título da postagem, Adicionar aba  

---

## 2. Expansão global — 17 idiomas

### Registro central
- **`config/supported-locales.json`** — fonte única para PHP e scripts Python  
- **`includes/locale-registry.php`** — carrega JSON sem acoplar textdomains  
- **`scripts/locale_config.py`** — espelho para pipelines de build  

### Idiomas do produto (16 packs + English International)

| Código | Idioma | Status |
|--------|--------|--------|
| `pt_BR` | Português (Brasil) | release gate |
| `pt_PT` | Português | release gate |
| `it_IT` | Italiano | release gate |
| `fr_FR` | Francês | beta |
| `de_DE` | Alemão | beta |
| `es_ES` | Espanhol (Espanha) | beta |
| **`es_LA`** | Espanhol (América Latina) | beta — **código Freemius** |
| `ar` | Árabe | beta (RTL) |
| `ja_JP` | Japonês | beta |
| **`nl_NL`** | Holandês | beta |
| **`hi_IN`** | Hindi | beta — **MT completo** |
| **`ru_RU`** | Russo | beta |
| **`zh_CN`** | Chinês simplificado | beta |
| **`ko_KR`** | Coreano | beta |
| **`pl_PL`** | Polonês | beta |
| **`he_IL`** | Hebraico | beta — **MT completo + RTL** |
| `en_GB` | English (International) | baseline |

**Aliases:** `es_419` / `es_MX` → `es_LA` · `he` / `iw` → `he_IL` · `zh` → `zh_CN` · `ja` → `ja_JP`

### Catálogos gerados
- **70 arquivos `.po`** · **70 `.mo`** · **36 `.json`**
- Shape dividers para todos os locales transponíveis (`config/shape-divider-labels.json`)

---

## 3. Hindi (`hi_IN`) — prioridade comercial

### Machine translation
| Domínio | Entradas MT |
|---------|-------------|
| breakdance-hi_IN.po | ~850 |
| breakdance-builder-hi_IN.po | ~1.285 |
| breakdance-elements-hi_IN.po | ~2.237 |
| **Total hi_IN** | **~4.602** |

### Runtime prioritário
- **`config/hi_IN-priority-strings.json`** — **185 strings**
  - 31 labels de UI/formulário (Form Builder, Submit, Contact Form…)  
  - **154 nomes de elementos** em devanagari  
- **`includes/priority-locale-i18n.php`** — injeta JSON no dicionário do editor  

### UI do plugin
- `breakdance-languages-hi_IN.po` — **100% traduzido** (painel Breakdance → Idiomas)

### Placeholders residuais
- **1** aviso beta (hífen colado em `%s-created` — espelha o inglês)

---

## 4. Hebraico (`he_IL`) — RTL + MT

### Suporte RTL
- **`includes/rtl-support.php`** — detecta `he_IL`/`ar`, classe `bdl-rtl-locale` no admin  
- **`admin/assets/settings-tab.css`** — propriedades lógicas (`inset-inline-*`, `text-align: start`, `border-inline-start`)  
- **`includes/builder-sync.php`** — toast do builder com CSS lógico  

### Machine translation
| Domínio | Entradas MT |
|---------|-------------|
| Catálogo inicial | ~4.598 |
| Rodada de limpeza + glossário | +174 |
| **Total aproximado** | **~4.770** |

### Correções pós-MT
- **`scripts/fix-rtl-mt-artifacts.py`** — remove ZWSP (`\u200b`) que o Google Translate inseriu antes de `%s`  
- **`scripts/apply-manual-glossary.py`** — Font Size, Product Gallery, Scroll Progress, strings do painel Idiomas  
- **`scripts/fix-placeholder-spacing.py`** — 55 entradas corrigidas (he_IL + hi_IN)  
- Placeholders: **35 → 1** (beta OK)

### Runtime prioritário
- **`config/he_IL-priority-strings.json`** — **159 nomes de elementos** em hebraico  
- Dicionário Form Builder em `form-builder-i18n.php` (`he_IL` map)  

### UI do plugin
- `breakdance-languages-he_IL.po` — **100% traduzido**

---

## 5. Freemius e geolocalização

| Arquivo | Função |
|---------|--------|
| `config/freemius-geolocation-locales.json` | Mapa país ISO → locale (`IN`→`hi_IN`, `IL`→`he_IL`, `NL`→`nl_NL`, `CN`→`zh_CN`, América Latina→`es_LA`) |
| `includes/geolocation-locale.php` | Sugestão de locale em modo **auto** via billing Freemius + `Accept-Language` |
| `translation-fallbacks.json` | Aliases WordPress → locale canônico |

---

## 6. Outros módulos de runtime (sessões anteriores mantidos)

| Módulo | Arquivo |
|--------|---------|
| Mídia / tipos dinâmicos | `includes/media-i18n.php` |
| Estilos globais / formulários | `includes/global-settings-i18n.php` |
| Design library | `includes/design-library.php` |
| Shape dividers | `includes/shape-dividers.php` + `config/shape-divider-labels.json` |
| Editor overrides | `includes/editor-overrides.php` |

**Editor override locales:** `pt_BR`, `pt_PT`, `ja_JP`, `he_IL`, `hi_IN`, `ar`

---

## 7. Scripts novos ou atualizados

| Script | Uso |
|--------|-----|
| `bootstrap-locale-catalogues.py` | Cria PO/JSON/MO para locales novos |
| `mt-untranslated-locale.py` | MT em lote (msgstr == msgid) |
| `expand-priority-element-names.py` | Gera JSON de nomes de elementos |
| `expand-hi-in-element-names.py` | Atalho Hindi |
| `add-form-builder-po-batch.py` | +27 chaves pt_BR elements |
| `reset-form-builder-msgstr.py` | Evita cópia pt_BR em outros locales |
| `fix-rtl-mt-artifacts.py` | Limpa ZWSP e `%N$` quebrados |
| `apply-manual-glossary.py` | Glossário manual hi_IN/he_IL |
| `locale_config.py` | Registry compartilhado |
| `sync-locale-parity.py` | Atualizado para todos os locales |
| `verify-catalogues.py` | 18 locales |
| `validate-all.py` | Gate inalterado — PASS |

---

## 8. Arquivos PHP novos

```
includes/locale-registry.php
includes/geolocation-locale.php
includes/rtl-support.php
includes/priority-locale-i18n.php
includes/form-builder-i18n.php      (expandido)
```

`builder-languages-breakdance.php` — carrega os módulos acima na ordem correta.

---

## 9. Estado de validação (final)

```
[1/4] Catalogue structure     PASS
[2/4] Compiled MO files       PASS (54 core .mo)
[3/4] Placeholder QA gate     PASS (pt_BR, pt_PT, it_IT = 0)
[4/4] Shape divider labels    PASS (16 locales)

OVERALL: PASS
```

### Placeholders beta (aceitável)

| Locale | Placeholders | Notas |
|--------|--------------|-------|
| hi_IN | 1 | hífen técnico em string longa |
| he_IL | 1 | espaço em plural hebraico |
| nl_NL, ko_KR, zh_CN, pl_PL, ru_RU, es_LA | 2 | MT residual |
| ja_JP | 61 | revisão humana recomendada |
| de_DE, ar | 37–47 | MT residual |

### Entradas ainda em inglês (beta)
- Termos de marca: FacetWP, nomes próprios  
- ~126 elements + ~30 builder em `he_IL` (termos técnicos curtos que o MT rejeitou)  
- ~41 elements em `hi_IN` — mesma categoria  

**Não afeta release gate nem estabilidade do plugin.**

---

## 10. Checklist de teste manual

1. **Breakdance → Idiomas** — confirmar 17 opções + “Usar idioma do perfil”  
2. **pt_BR** — Form Builder → painéis traduzidos; dropdown “2 selecionados”  
3. **hi_IN** — hard refresh → nomes de elementos em devanagari  
4. **he_IL** — painel RTL; Form Builder em hebraico  
5. **es_LA** — selecionar e salvar; perfil WP com `es_419` deve resolver para `es_LA`  
6. **Estilos globais → Formulários** — labels pt_BR (Horizontal X / Vertical Y)  

---

## 11. Comandos úteis

```powershell
cd wp-content/plugins/builder-languages-breakdance

# Validação completa
python scripts/validate-all.py

# Recompilar MO após editar PO
python scripts/compile-mo.py

# MT para locale beta
python scripts/mt-untranslated-locale.py --locale ko_KR --json

# Corrigir placeholders RTL/MT
python scripts/fix-rtl-mt-artifacts.py --locale he_IL
python scripts/fix-placeholder-spacing.py --locale he_IL --locale hi_IN

# Novo locale a partir do baseline
python scripts/bootstrap-locale-catalogues.py --locale xx_XX
```

---

## 12. Próximos passos opcionais (não bloqueantes)

1. Revisão humana de `ja_JP` (61 placeholders)  
2. MT com `--translate` para `ko_KR`, `zh_CN`, `nl_NL` (mercado asiático/europeu)  
3. Entradas PO para Form Builder em `pt_PT` / `it_IT` via `sync-locale-parity --elements-only`  
4. Atualizar `docs/DEV-SETUP.md` com lista dos 17 idiomas e scripts novos  
5. Revisar ~126 strings técnicas restantes em `he_IL` elements (baixa prioridade)  

---

## 13. Conclusão

O plugin está **estável**, **validado** e **pronto para comercialização global** com:

- **17 idiomas** no dropdown  
- **Hindi e Hebraico** com catálogos MT completos e runtime prioritário  
- **RTL** seguro para Hebraico/Árabe  
- **es_LA / zh_CN / nl_NL** mapeados para Freemius  
- **Form Builder pt-BR** coberto em PHP + JS + PO  

Nenhuma regressão nos locales de release gate. Recomenda-se hard refresh no builder após trocar idioma.

---

*Gerado automaticamente após pipeline de finalização — sparklean-02, julho/2026.*
