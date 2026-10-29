# Frontend Vue do Promex

O frontend público foi migrado de forma progressiva para Vue 3, sem mover as
regras de saldo, apostas ou autenticação para o navegador.

- Rotas Vue padrão: `/`, `/sports`, `/profile`, `/vip` e `/affiliates`.
- As rotas `/app`, `/app/jogos`, `/app/esportes`, `/app/perfil`, `/app/vip` e
  `/app/afiliados` permitem navegação interna sem recarregar o documento.
- Lançadores de jogos, Liteback e o CRM de parceiros continuam em Laravel/Blade
  para preservar as integrações existentes.
- O Vue consome apenas `/spa/api/*` e o catálogo público incremental em
  `/catalog.json`; qualquer crédito ou aposta continua sendo validado no Laravel.

## Build

Após alterar qualquer arquivo em `resources/spa/`, execute:

```bat
cd casino
npm run spa:build
```

O build é publicado em `../minimal/spa/` com nomes versionados. O `start.bat`
normal não precisa de Node para servir o site depois que esse build existe.

Para desenvolvimento do bundle, use `npm run spa:dev` em outro terminal.
