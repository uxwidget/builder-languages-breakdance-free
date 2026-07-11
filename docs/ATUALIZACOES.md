# Como documentar atualizações

Este plugin é de tradução: o histórico de mudanças mora no **repositório**, não no painel do WordPress. Cada release (ou correção relevante) deve ficar registrada antes do push no GitHub.

Relacionado: [CHANGELOG.md](./CHANGELOG.md) · [AMBIENTES.md](./AMBIENTES.md) · [BLB-MANIFEST.md](./BLB-MANIFEST.md)

---

## Fonte oficial

| Artefato | Obrigatório? | Uso |
| --- | --- | --- |
| [CHANGELOG.md](./CHANGELOG.md) | **Sim** | Lista por versão (`ux-0.1.x`) o que mudou e por quê |
| Header `Version` + `BREAKDANCE_LANGUAGES_VERSION` | **Sim** | Versão exibida no WP e no manifesto |
| `.update.blb` | **Sim** a cada bump | Regenerar com `python scripts/blb-manifest.py write` |
| Commit + push no GitHub | **Sim** quando for publicar a mudança | Repo privado |
| Relatório longo (`docs/RELATORIO-*.md`) | Opcional | Só para ondas grandes (ex.: QA de todos os locales) |

O painel admin **não** precisa de changelog interno: produto de tradução, sem fila de suporte.

---

## Checklist por bump

1. Implementar a correção/feature.
2. Subir a versão (ex.: `ux-0.1.3` → `ux-0.1.4`) no arquivo principal.
3. Acrescentar seção no topo de `docs/CHANGELOG.md` (bullet points curtos, em pt-BR).
4. Regenerar e verificar o manifesto:
   ```text
   python scripts/blb-manifest.py write --channel stable --notes "ux-0.1.4"
   python scripts/blb-manifest.py verify
   ```
5. Commit com mensagem clara (por quê, não só o quê).
6. Push para `origin/main`.
7. (Venda) Empacotar ZIP: `python scripts/pack-release.py` — ver [PACK-RELEASE.md](./PACK-RELEASE.md).

Não commitar: `.blb-secret`, `config/freemius.php`, nem chaves Freemius.

---

## Histórico recente (referência rápida)

| Versão | Foco |
| --- | --- |
| `ux-0.1.6` | Remove pacote `en_US`; Breakdance nativo; só `en_GB` como inglês do plugin |
| `ux-0.1.5` | `pack-release.py` — ZIP comercial via `.distignore` |
| `ux-0.1.4` | Fallback só com catálogo presente; merge JED sem sobrescrever domínio `breakdance` |
| `ux-0.1.3` | UI da tela Languages no idioma escolhido (`settings-ui-strings.json`) |
| `ux-0.1.2` | Categorias do painel Adicionar traduzidas |
| `ux-0.1.1` | Botão “Atualizar página” após language pack do WP |
| `ux-0.1.0` | Release comercializável inicial + QA placeholders |

Detalhe completo: [CHANGELOG.md](./CHANGELOG.md).

---

## Site de venda vs documentação

O WordPress **novo** da versão de venda serve só para Freemius/checkout.  
A documentação das atualizações continua neste repo — o site de venda não substitui o CHANGELOG.
