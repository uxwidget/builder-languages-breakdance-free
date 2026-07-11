# Builder Languages for Breakdance — FAQ

## Funciona com o Breakdance Builder?

Sim. O plugin foi feito especificamente para o Breakdance Builder e seus elementos first-party.

## É feito pela equipe do Breakdance?

Não. É um produto independente da **UX Widget**. Não é afiliado, endossado nem mantido pela equipe do Breakdance.

## Preciso do Breakdance instalado?

Sim. Sem o Breakdance ativo, o plugin mostra um aviso no admin e não carrega language packs.

## Traduz o WooCommerce?

Em geral, não. Strings do WooCommerce usam o text domain `woocommerce` e vêm dos language packs do WooCommerce. Este plugin foca em Breakdance e Breakdance Elements.

## Quantos idiomas estão incluídos?

17 idiomas de produto (`en_GB` = English International). O `en_US` é nativo do Breakdance e **não** entra na lista suportada. Lista completa em `config/supported-locales.json` e no [README](../README.md).

## Meu perfil está em inglês, mas escolhi português no plugin. Por que o builder ainda aparece parcialmente em inglês?

1. Recarregue o builder (`Ctrl+Shift+R`).
2. Confirme se o perfil foi sincronizado (idioma explícito sincroniza o perfil).
3. Limpe cache de plugins/CDN.
4. Algumas strings hardcoded do Breakdance ainda não passam por gettext — o plugin cobre o que está nos catálogos e nas camadas de runtime.

## O que é `es_LA`?

Espanhol da América Latina. Aliases como `es_419` e `es_MX` resolvem para `es_LA`.

## Hebraico e árabe são RTL?

Sim. Há suporte RTL no painel do plugin (`includes/rtl-support.php` + CSS lógico). O builder segue o comportamento RTL do WordPress/Breakdance para esses locales.

## O Freemius slug ainda é `breakdance-languages`?

Sim, de propósito. Text domain, Freemius e arquivos `breakdance-languages-*.po` mantêm o slug antigo para não quebrar licenças e traduções do painel. O **nome exibido** e a pasta do plugin são **Builder Languages for Breakdance** / `builder-languages-breakdance`.

## Onde fica o diagnóstico?

Em `Breakdance > Languages`.

## Posso editar as traduções?

Sim. Edite os `.po` em `languages/`, compile `.mo` e regenere JSON quando necessário. Veja o [USER-GUIDE.md](./USER-GUIDE.md).
