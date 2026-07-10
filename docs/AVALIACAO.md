# Builder Languages for Breakdance — Avaliação técnica

**Data:** 2026-07-10 (reavaliação pós-expansão de locales)  
**Versão:** `ux-0.1.0`  
**Breakdance de referência:** `2.8.0`  
**Pasta:** `wp-content/plugins/builder-languages-breakdance`

---

## Resumo

Plugin complementar da **UX Widget** que adiciona pacotes de idioma ao **Breakdance Builder** sem alterar o core. Produto independente, não afiliado à equipe Breakdance.

Nome de exibição: **Builder Languages for Breakdance**. Slug Freemius / text domain interno: `breakdance-languages` (mantido de propósito).

**Veredito:** pronto para beta comercial controlado. Gate `pt_BR` / `pt_PT` / `it_IT` passa com 0 placeholders. Locales beta usáveis via MT; revisão humana de copy ainda recomendada antes de campanha ampla.

---

## Arquitetura (camadas)

| Camada | Mecanismo | Arquivos-chave |
|--------|-----------|----------------|
| Admin / PHP | `load_textdomain` prioridade 20 | `breakdance-*.mo`, `breakdance-elements-*.mo` |
| Builder JS | filtro `breakdance_i18n_json` | `breakdance-*.json`, `breakdance-elements-*.json` |
| Controles / Form | `breakdance_element_controls` + JS overrides | `form-builder-i18n.php`, `editor-overrides.php` |
| Priority / RTL | dicionários + CSS lógico | `priority-locale-i18n.php`, `rtl-support.php` |
| Locales | registry central | `config/supported-locales.json`, `locale-registry.php` |
| Manifesto | `.update.blb` assinado | `blb-manifest.php`, `scripts/blb-manifest.py` |
| Licensing | Freemius scaffold | `freemius-init.php`, `config/freemius.php` (local) |

### Idiomas

17 de produto + baseline `en_US` / `en_GB`. Ver `config/supported-locales.json`.

---

## Evolução relevante

| Item | Estado em 2026-07-10 |
|------|----------------------|
| Dependência Breakdance | Guard + `Requires Plugins` |
| Form Builder | Camada dedicada PHP/JS |
| Locales | 17 (antes ~9) |
| Hindi / Hebraico | Priority + RTL |
| `es_LA` | Alias LATAM |
| Rename de marca | Pasta/nome exibido atualizados |
| Repo | GitHub **privado** |
| Secret BLB / Freemius | Gitignored |

---

## Estrutura do repositório

```
builder-languages-breakdance/
├── builder-languages-breakdance.php
├── .update.blb
├── admin/
├── assets/
├── config/                 # locales, priority, freemius example
├── includes/               # runtime, i18n, freemius, blb
├── languages/              # po/mo/json
├── scripts/                # MT, QA, blb-manifest, validate-all
├── docs/
├── marketing/
└── vendor/freemius/
```

---

## Riscos / resíduos

- Placeholders MT em locales beta (`zh_CN`, `ja_JP`, `de_DE`, etc.)
- Strings hardcoded do Breakdance fora dos `.pot`
- WooCommerce via text domain próprio
- Não renomear Freemius slug sem plano de migração

---

## Próximos passos sugeridos

1. QA manual Form Builder nos locales novos  
2. Reduzir resíduos de placeholder nos betas  
3. Publicar LP / Freemius production com nome **Builder Languages for Breakdance**  
4. Regenerar `.update.blb` a cada bump de versão  

Detalhes da onda 10/07: [RELATORIO-ATUALIZACOES-2026-07-10.md](./RELATORIO-ATUALIZACOES-2026-07-10.md).  
Índice: [INDEX.md](./INDEX.md).
