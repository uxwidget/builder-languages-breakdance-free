# Empacotar ZIP comercial (release)

Gera o ZIP de distribuição respeitando [`.distignore`](../.distignore), para não incluir cache, scripts, marketing nem secrets por acidente.

Relacionado: [AMBIENTES.md](./AMBIENTES.md) · [ATUALIZACOES.md](./ATUALIZACOES.md) · [LICENSING.md](./LICENSING.md)

---

## Comando

```text
python scripts/pack-release.py
```

Saída padrão: `dist/builder-languages-breakdance-{versão}.zip`  
Pasta raiz dentro do ZIP: `builder-languages-breakdance/`

### Opções

| Flag | Efeito |
| --- | --- |
| `--dry-run` | Lista arquivos que entrariam no ZIP (não grava) |
| `--list-excluded` | Lista caminhos excluídos |
| `--out caminho.zip` | Define o caminho do ZIP |
| `--freemius-config caminho.php` | Injeta `config/freemius.php` de **produção** no ZIP |

---

## O que nunca entra no ZIP

Via `.distignore` + exclusões fixas do script:

- `scripts/`, `marketing/`, `translation-cache.json`
- `.blb-secret*`, `config/freemius.php` de dev
- `.git/`, `dist/`, `__pycache__/`

O SDK Freemius em `vendor/freemius/` **entra** no pacote.

---

## Freemius no build de venda

Por padrão o ZIP **não** leva `config/freemius.php` (é secret de ambiente).

Para o site WordPress **separado** de venda:

```text
python scripts/pack-release.py --freemius-config C:\caminho\seguro\freemius.production.php
```

Ou configure as constantes no servidor / deploy Freemius sem versionar a key. Ver [FREEMIUS-SETUP.md](./FREEMIUS-SETUP.md).

---

## Fluxo sugerido

1. Bump + CHANGELOG + `.update.blb` ([ATUALIZACOES.md](./ATUALIZACOES.md))
2. `python scripts/validate-all.py` (gate)
3. `python scripts/pack-release.py` (ou com `--freemius-config`)
4. Instalar o ZIP no **WP novo** de venda ([AMBIENTES.md](./AMBIENTES.md))
5. Testar checkout / licença ([FREEMIUS-TESTING.md](./FREEMIUS-TESTING.md))

`dist/` fica fora do Git (`.gitignore`).
