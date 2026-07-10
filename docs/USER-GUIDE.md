# Builder Languages for Breakdance — Guia do usuário

O **Builder Languages for Breakdance** adiciona pacotes de idioma para o Breakdance Builder, telas de admin/PHP do Breakdance e elementos first-party.

## O que traduz

- Labels da interface do Builder
- Nomes de elementos
- Controles de elementos
- Seções de presets
- Strings de admin/PHP do Breakdance
- Strings dos Breakdance Elements first-party
- Form Builder (ações, e-mail, CSRF, reCAPTCHA, etc. — via catálogo + runtime)

## O que não substitui

Alguns textos podem continuar em inglês quando forem:

- Hardcoded em JavaScript compilado do Breakdance
- Carregados por plugins de terceiros
- Do WooCommerce ou de outro text domain
- Código custom, templates ou snippets
- Conteúdo salvo no banco WordPress (posts, páginas, formulários)

## Escolha de idioma

Vá em `Breakdance > Languages` para escolher o idioma do Breakdance Builder.

### Idioma explícito do Builder (recomendado)

Escolha um locale específico (por exemplo `Português (Brasil)`). O plugin salva a preferência do builder **e sincroniza o locale do perfil WordPress** do usuário atual, para Breakdance e WordPress ficarem alinhados.

Depois, recarregue o Breakdance Builder (`Ctrl+Shift+R` ou feche e reabra a aba).

### Modo Auto

**Usar idioma do perfil WordPress** faz o builder seguir `Usuários > Perfil > Idioma`. No modo Auto o plugin não altera o perfil.

### Depois de mudar o idioma

Salvar um idioma novo **não** recarrega automaticamente uma sessão aberta do builder. Recarregue manualmente.

A tela de Languages mostra o idioma do perfil WordPress e avisa se ainda divergir da escolha do builder.

### Planos

- **Instalações licenciadas:** qualquer idioma suportado, independente do perfil — alinhar os dois ainda é recomendado.
- **Planos free / limitados:** as traduções seguem o idioma do perfil quando o locale estiver no plano.

Se o arquivo exato do locale não existir, o plugin pode usar mapeamentos de `translation-fallbacks.json` (ex.: `es_MX` / `es_419` → `es_LA`).

## Diagnósticos

Abra `Breakdance > Languages` para verificar:

- Detecção do Breakdance
- Versão do Breakdance
- Locale do usuário WordPress
- Language pack resolvido
- Arquivos `.mo` e `.json` necessários
- Status da configuração comercial Freemius

## Atualizar traduções

Quando uma nova versão for instalada:

1. Atualize o plugin.
2. Limpe caches.
3. Abra o Breakdance Builder.
4. Faça hard refresh no navegador.

## Correções manuais de tradução

Edite os arquivos `.po` em:

`wp-content/plugins/builder-languages-breakdance/languages`

Depois de editar:

1. Recompile o `.mo` correspondente (`python scripts/compile-mo.py`).
2. Regenere JSON do Builder quando a origem for catálogo de builder.
3. Limpe cache antes de testar.

## Tipos de arquivo

- `.po` — catálogo editável
- `.mo` — gettext compilado (WordPress/PHP)
- `.json` — payload JavaScript do Breakdance Builder
- `.update.blb` — manifesto assinado de versão/canal (interno UX Widget)
