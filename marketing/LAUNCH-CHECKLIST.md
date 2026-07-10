# Builder Languages for Breakdance — Checklist de lançamento

## Produto

- [ ] Versão `ux-0.1.0` (ou bump) com `validate-all.py` = PASS
- [ ] Gate `pt_BR` / `pt_PT` / `it_IT` = 0 placeholders
- [ ] `.update.blb` regenerado (`python scripts/blb-manifest.py write`)
- [ ] ZIP comercial sem `.blb-secret` / `config/freemius.php` de dev (`.distignore`)
- [ ] Nome exibido: **Builder Languages for Breakdance**
- [ ] Freemius slug permanece `breakdance-languages`

## Site / LP (UX Widget)

- [ ] Seção **Builder Languages for Breakdance** com CTA, preços e FAQ
- [ ] URL sugerida: `uxwidget.com/builder-languages-breakdance` (redirect do slug antigo se existir)
- [ ] Meta title/description SEO
- [ ] Disclaimer de produto independente (não afiliado ao Breakdance)

## Freemius

- [ ] Título do produto atualizado no dashboard
- [ ] Planos Personal / Business / Agency
- [ ] Checkout production testado
- [ ] E-mails sandbox/production revisados com o nome novo
- [ ] Ver [FREEMIUS-SETUP.md](../docs/FREEMIUS-SETUP.md) e [FREEMIUS-TESTING.md](../docs/FREEMIUS-TESTING.md)

## QA em site limpo

- [ ] Seguir [DEV-SETUP.md](../docs/DEV-SETUP.md)
- [ ] Trocar idioma em `Breakdance > Languages` + hard refresh
- [ ] Smoke Form Builder em `pt_BR`
- [ ] Smoke RTL `he_IL` ou `ar`
- [ ] Diagnósticos da tela Languages OK

## Docs

- [ ] [INDEX.md](../docs/INDEX.md) atualizado
- [ ] README pt-BR
- [ ] CHANGELOG da versão
- [ ] Repo GitHub **privado**; secrets fora do Git

## Pós-lançamento

- [ ] Monitorar tickets de locale / placeholders
- [ ] Próximo bump regenera `.update.blb` + changelog
