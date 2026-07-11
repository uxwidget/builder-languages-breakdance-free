# Builder Languages for Breakdance — Instalação

## Requisitos

- WordPress 6.0 ou mais recente
- PHP 7.4 ou mais recente
- Breakdance Builder instalado e ativo
- Locale do usuário WordPress suportado pelo plugin

## Idiomas incluídos

- Português Brasil (`pt_BR`)
- Português Portugal (`pt_PT`)
- Italiano (`it_IT`)
- Francês (`fr_FR`)
- Alemão (`de_DE`)
- Espanhol Espanha (`es_ES`)
- Espanhol América Latina (`es_LA`)
- Holandês (`nl_NL`)
- Polonês (`pl_PL`)
- Russo (`ru_RU`)
- Árabe (`ar`)
- Hebraico (`he_IL`)
- Hindi (`hi_IN`)
- Japonês (`ja_JP`)
- Coreano (`ko_KR`)
- Chinês Simplificado (`zh_CN`)
- Inglês Internacional (`en_GB`) — único inglês fornecido pelo plugin
- Inglês Estados Unidos (`en_US`) — **nativo do Breakdance**; não é pacote deste plugin

Lista canônica: `config/supported-locales.json`.

## Instalar a partir do ZIP

1. Baixe o ZIP na conta UX Widget ou no e-mail de compra.
2. No WordPress, vá em `Plugins > Adicionar novo plugin`.
3. Clique em `Enviar plugin`.
4. Selecione o arquivo `builder-languages-breakdance.zip` (ou o nome do release comercial).
5. Clique em `Instalar agora`.
6. Ative **Builder Languages for Breakdance**.

> ZIPs antigos podem ainda se chamar `breakdance-languages.zip`. O conteúdo é o mesmo produto; prefira o nome novo nos releases atuais.

## Definir o idioma

O plugin segue o locale do usuário WordPress e a preferência em `Breakdance > Languages`.

Para o site inteiro:

1. Vá em `Configurações > Geral`.
2. Altere `Idioma do site`.
3. Salve.

Só para o seu usuário:

1. Vá em `Usuários > Perfil`.
2. Altere `Idioma`.
3. Salve.

Ou escolha o idioma do Builder em `Breakdance > Languages` (recomendado). Com um idioma explícito, o plugin também sincroniza o locale do perfil do usuário atual.

## Limpar cache

Após ativar ou atualizar:

1. Limpe o cache de plugins WordPress.
2. Limpe cache de servidor/CDN, se houver.
3. Abra o Breakdance Builder em uma nova aba.
4. Faça hard refresh:
   - Windows/Linux: `Ctrl + F5` ou `Ctrl + Shift + R`
   - macOS: `Cmd + Shift + R`

## Verificar a instalação

Abra uma página no Breakdance Builder. Labels, controles, elementos e textos de admin devem aparecer no idioma escolhido.

Confirme também em `Breakdance > Languages` (diagnósticos do plugin).

Se a interface continuar em inglês, verifique:

- Idioma do perfil WordPress
- Se o idioma está na lista suportada
- Se o Breakdance Builder está ativo
- Se cache do navegador/servidor está servindo JavaScript antigo
