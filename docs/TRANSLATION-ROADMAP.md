# Roteiro de paridade de tradução

Como levar cada idioma ao mesmo nível do **pt_BR** (~4644 entradas PO + patches de runtime).

Relacionado: [DEV-SETUP.md](./DEV-SETUP.md) · [COMPATIBILITY.md](./COMPATIBILITY.md) · [AVALIACAO.md](./AVALIACAO.md)

Última atualização: **2026-07-09**

---

## Números reais hoje

| Camada | pt_BR | Outros (fr/de/es/ar/ja/it) |
|--------|-------|----------------------------|
| breakdance.po | 1080 | 1080 |
| breakdance-builder.po | 1286 | 1286 |
| breakdance-elements.po | **2229** | **2211** (−18 chaves) |
| breakdance-languages.po (painel plugin) | **49** | **ausente** (exc. pt_PT: 31, it_IT: 49) |
| **Total PO** | **~4644** | **~4577** |

Os **~4577** já estão **100% preenchidos** (machine translation). A diferença para pt_BR não é “falta traduzir metade do catálogo” — é **paridade de chaves + qualidade + runtime**.

As **18 chaves** a mais no pt_BR (elements) vêm de strings de layout (`Flex Start`, `Grid Template`, etc.) adicionadas manualmente — faltam nos outros locales.

---

## Modelo em 5 camadas (replicar pt_BR)

```
Camada 1 — Catálogo PO/MO/JSON     ██████████  todos ~100% MT
Camada 2 — Paridade de chaves      ████░░░░░░  pt_BR +18 elements
Camada 3 — Qualidade MT            ███░░░░░░░  placeholders, copy
Camada 4 — Patches de runtime      ██░░░░░░░░  shapes, overrides, borders
Camada 5 — QA manual no builder    ░░░░░░░░░░  contínuo
```

### Camada 1 — Catálogo (já feito)

```powershell
python scripts/verify-catalogues.py
python scripts/compile-mo.py
```

Todos os 10 locales: `.po` + `.json` + `.mo` nas 3 camadas Breakdance.

### Camada 2 — Paridade de chaves vs pt_BR

1. Rodar relatório:

```powershell
python scripts/compare-locale-coverage.py
```

2. Para cada locale com `−N PO keys`:
   - Copiar msgids ausentes do `breakdance-elements-pt_BR.po`
   - Traduzir (MT ou manual) e regenerar JSON:

```powershell
python scripts/generate-locale.py --target fr_FR --json-only
python scripts/compile-mo.py
```

3. Criar `breakdance-languages-{locale}.po` para **fr_FR, de_DE, es_ES, ar, ja_JP** (painel Idiomas) — hoje só pt_BR, pt_PT, it_IT têm.

### Camada 3 — Qualidade (copiar pipeline pt_BR/de_DE)

Ordem por locale:

```powershell
python scripts/fix-placeholder-spacing.py --locale fr_FR
python scripts/compare-locale-coverage.py
python scripts/qa-placeholders.py --locale fr_FR
```

Passagens manuais curadas (como `de_DE-manual-pass.py`) para strings visíveis: Pro, WooCommerce, delete, import/export, sessão.

**Gate de release:** `pt_BR`, `pt_PT`, `it_IT` = 0 placeholders. Demais: tendência ↓.

### Camada 4 — Patches de runtime (fora do PO)

| Patch | pt_BR | Replicar para |
|-------|-------|----------------|
| `config/shape-divider-labels.json` | 53 labels | pt_PT, fr_FR, de_DE, es_ES, ar, ja_JP, it_IT |
| `includes/editor-overrides.php` | sim (+ pt_PT, ja_JP) | fr, de, es, ar, it (opcional) |
| `includes/design-library.php` | mapas UI | já parcial — revisar |
| Border CSS keywords (solid, dashed…) | não | override futuro se quiser |

Shape dividers — adicionar locale no JSON:

```json
"pt_PT": { "Angle1": "Ângulo 1", ... }
```

Validar:

```powershell
python scripts/verify-shape-dividers.py
```

### Camada 5 — QA manual

Por locale, no builder (hard refresh):

- [ ] Painel Adicionar (elementos básicos)
- [ ] Guias Typography / Size / Borders
- [ ] Divisores de forma (labels traduzidos)
- [ ] Avisos Pro / sessão expirada
- [ ] Design Library (categorias)

---

## Ordem sugerida de execução

1. **Sync 18 chaves** elements → todos os locales  
2. **breakdance-languages PO** → fr, de, es, ar, ja  
3. **Shape dividers JSON** → pt_PT, depois europeus, depois ar/ja  
4. **fix-placeholder-spacing** + manual pass → de_DE (feito parcial), fr_FR, es_ES, ar  
5. **compare-locale-coverage** = zero gaps  
6. **validate-all.py** = PASS  

---

## Comando único de auditoria

```powershell
python scripts/validate-all.py
python scripts/compare-locale-coverage.py
```

---

## Definição de “100% traduzido” (fechar a lógica)

Um locale está **fechado** quando:

1. `compare-locale-coverage.py` → paridade OK vs pt_BR (mesmo número de chaves PO)  
2. `verify-catalogues.py` → OK  
3. `verify-shape-dividers.py` → 53/53 labels  
4. `breakdance-languages-{locale}.po` existe  
5. Placeholder QA dentro da meta acordada (gate ou beta)  
6. Checklist manual Camada 5 passou  

Isso alinha **quantidade** (~4644 entradas) e **cobertura funcional** (runtime + QA).
