# Manifesto privado `.update.blb`

Arquivo de metadados do **Builder Languages for Breakdance** (extensão `.blb`) usado pelo seu tooling de release/update.

## Privacidade (obrigatório)

| Item | No GitHub privado? | No ZIP público do plugin? |
|------|--------------------|---------------------------|
| `.update.blb` (assinado) | Sim | Sim (vai no release) |
| `.blb-secret` | **Nunca** | **Nunca** |
| `BLB_MANIFEST_SECRET` (env/CI) | Só em secrets do CI | Não |
| `config/freemius.php` | **Nunca** | **Nunca** |

O repositório no GitHub deve ser **privado**. O secret fica só em:

1. arquivo local `.blb-secret` (gitignored), ou  
2. variável de ambiente / GitHub Actions secret `BLB_MANIFEST_SECRET`

## Formato

```
BLB1.<payload_zlib_base64url>.<hmac_sha256_hex>
```

## Gerar / verificar

```powershell
cd wp-content/plugins/builder-languages-breakdance

# Cria .blb-secret automaticamente na primeira vez (se não existir)
python scripts/blb-manifest.py write --channel stable --notes "ux-0.1.0"

python scripts/blb-manifest.py verify
python scripts/blb-manifest.py read
```

## PHP

```php
$manifest = breakdance_languages_read_blb_manifest(false); // decode
$trusted  = breakdance_languages_read_blb_manifest(true);  // precisa do secret
$version  = breakdance_languages_blb_reported_version();
```

No servidor de update privado, defina:

```php
define('BLB_MANIFEST_SECRET', 'mesmo-valor-do-.blb-secret');
```

Freemius continua cuidando do update comercial; `.update.blb` é a camada interna UX Widget.
