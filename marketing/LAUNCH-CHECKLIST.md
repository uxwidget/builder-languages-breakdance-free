# Builder Languages for Breakdance — Checklist de lançamento

## Produto

- [x] ZIP comercial `0.1.13` via `pack-release.py` — [PACK-RELEASE.md](../docs/PACK-RELEASE.md)
- [x] Nome exibido: **Builder Languages for Breakdance**
- [x] Freemius slug permanece `breakdance-languages`
- [x] Public Key `pk_e984dedde8057992b2e0735383e70` no `config/freemius.php` + ZIP
- [x] Versão SemVer (Freemius rejeita `ux-0.x.x`)
- [ ] Gate `pt_BR` / `pt_PT` / `it_IT` = 0 placeholders (validar no sparklean se necessário)

---

## Freemius + LP

### Freemius

- [x] Título: **Builder Languages for Breakdance**
- [x] Planos: Personal $39 (1) / Studio $79 (5) / Agency $179 (20) / Pro $299 (50) — **sem ilimitado**
- [x] Pricing IDs + Checkout Links Production em [FREEMIUS-TESTING.md](../docs/FREEMIUS-TESTING.md)
- [x] SDK Integration: parent `'breakdance'` · submenu `'breakdance-languages'` · path `admin.php?page=breakdance-languages`
- [x] Deploy ZIP `0.1.12` Released (build antiga removida)
- [x] Teste **blb01**: Licença **Ativa** / Ambiente **Produção**
- [ ] Regenerar Product Secret Key (foi partilhada em chat de debug)
- [ ] Ver [FREEMIUS-SETUP.md](../docs/FREEMIUS-SETUP.md) · [PRICING.md](./PRICING.md)

### LP (UX Widget)

- [ ] Página `uxwidget.com/builder-languages-breakdance` — copy em [SALES-PAGE.md](./SALES-PAGE.md)
- [ ] 4 cards de preço ($39 / $79 / $179 / $299)
- [ ] CTAs com Checkout Links **Production**
- [ ] FAQ + disclaimer (independente, não afiliado ao Breakdance)
- [ ] Meta title/description SEO
- [ ] QA interno com link Sandbox (não publicar Sandbox na LP)
- [ ] Plugin no site LP atualizado para `0.1.12` (Public Key correta)

---

## Segunda (na empresa) — Marketplace Breakdance

Requer Breakdance Pro da empresa — **não bloqueia** Freemius/LP.

Copy + logos prontos: [marketplace/MARKETPLACE-SUBMIT.md](./marketplace/MARKETPLACE-SUBMIT.md)

- [x] Submissão / listagem no marketplace Breakdance (colar de MARKETPLACE-SUBMIT.md) — enviada; aguarda review manual
- [x] Preços alinhados: $39 / $79 / $179 / $299 (1 / 5 / 20 / 50) — documentados no submit pack
- [x] Buy button → LP UX Widget (checkout Freemius Production) — documentado no submit pack
- [x] Logos Author 64×64 + Add-On 256×256 em `marketing/marketplace/`
