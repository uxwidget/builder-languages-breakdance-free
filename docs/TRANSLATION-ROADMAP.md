# Roteiro de paridade de tradução

Como levar cada idioma ao mesmo nível do **pt_BR** (catálogos + runtime + QA).

Relacionado: [DEV-SETUP.md](./DEV-SETUP.md) · [COMPATIBILITY.md](./COMPATIBILITY.md) · [AVALIACAO.md](./AVALIACAO.md) · [RELATORIO-ATUALIZACOES-2026-07-10.md](./RELATORIO-ATUALIZACOES-2026-07-10.md)

Última atualização: **2026-07-10**

---

## Estado atual (resumo)

| Item | Status |
|------|--------|
| Locales de produto | **17** (`en_GB` = English International; `en_US` = nativo Breakdance, sem pacote nosso) |
| Catálogos Breakdance (3 camadas) | Preenchidos para todos os locales suportados |
| Painel `breakdance-languages-*.po` | Presente nos locales principais (incl. novos da onda 10/07) |
| Gate release | `pt_BR`, `pt_PT`, `it_IT` = **0** placeholders |
| Placeholders (todos os locales) | **0** após limpeza MT (2026-07-10) |
| Form Builder | Camada PHP + JS + PO (pt_BR e runtime) |
| Hindi / Hebraico | Priority JSON + RTL |
| `es_LA` | Cópia inteligente de `es_ES` |
| `zh_CN`, `ko_KR`, `nl_NL`, `ru_RU`, `pl_PL` | MT completo (usáveis; resíduos de placeholder possíveis) |

Fonte da verdade de locales: `config/supported-locales.json`.

---

## Modelo em 5 camadas

```
Camada 1 — Catálogo PO/MO/JSON     ██████████  todos preenchidos
Camada 2 — Paridade de chaves      ████████░░  sync residual vs pt_BR
Camada 3 — Qualidade MT            █████░░░░░  gate OK; beta com resíduos
Camada 4 — Patches de runtime      ███████░░░  form, shapes, RTL, priority
Camada 5 — QA manual no builder    ███░░░░░░░  contínuo por locale
```

### Camada 1 — Catálogo

```powershell
cd wp-content/plugins/builder-languages-breakdance
python scripts/verify-catalogues.py
python scripts/compile-mo.py
python scripts/validate-all.py
```

### Camada 2 — Paridade de chaves vs pt_BR

```powershell
python scripts/compare-locale-coverage.py
```

Para gaps: copiar msgids de `breakdance-elements-pt_BR.po`, traduzir, regenerar JSON.

### Camada 3 — Qualidade

```powershell
python scripts/fix-placeholder-spacing.py --locale LOCALE
python scripts/qa-placeholders.py --locale LOCALE
```

**Gate:** `pt_BR`, `pt_PT`, `it_IT` = 0. Demais: reduzir resíduos MT (atenção `de_DE`, `zh_CN`, `ja_JP`).

### Camada 4 — Runtime

| Patch | Arquivo |
|-------|---------|
| Form Builder | `includes/form-builder-i18n.php` |
| Overrides editor | `includes/editor-overrides.php` |
| Priority hi/he | `includes/priority-locale-i18n.php` |
| RTL | `includes/rtl-support.php` |
| Shape dividers | `config/shape-divider-labels.json` |
| Design library | `includes/design-library.php` |

### Camada 5 — QA manual (por locale)

- [ ] Painel Adicionar (elementos)
- [ ] Typography / Size / Borders
- [ ] Form Builder (ações, e-mail, CSRF)
- [ ] Divisores de forma
- [ ] Design Library
- [ ] RTL (ar / he_IL)

---

## Prioridade sugerida (pós 10/07)

1. Reduzir placeholders em `zh_CN`, `ko_KR`, `pl_PL`, `ja_JP`, `de_DE`
2. Paridade de chaves elements vs pt_BR em todos os locales
3. Shape dividers nos locales novos
4. QA manual Form Builder em `es_LA`, `hi_IN`, `he_IL`
5. Manter `validate-all.py` = PASS antes de cada release

---

## Definição de locale “fechado”

1. Paridade de chaves vs pt_BR  
2. `verify-catalogues.py` OK  
3. Shape dividers completos (quando aplicável)  
4. `breakdance-languages-{locale}.po` existe  
5. Placeholder QA dentro da meta (gate ou beta)  
6. Checklist Camada 5 passou  
