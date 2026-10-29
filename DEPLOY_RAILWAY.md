# Deploy no Railway

Este repositório está preparado para executar o Promex como aplicação PHP completa no Railway. O Railpack instala PHP, Composer e Node 22, instala as dependências de `casino/`, compila a SPA e inicia o roteador da aplicação na porta fornecida pela plataforma.

## Criar o serviço

1. No Railway, crie um projeto e escolha **Deploy from GitHub Repo**.
2. Selecione `marcusw3000/bettingB` e a branch `main`.
3. Use a raiz do repositório como **Root Directory** (`.`). Não use `casino`, pois os assets públicos e o roteador ficam na raiz.
4. Em **Variables**, cadastre as variáveis abaixo. O `railpack.json` já define o build e a inicialização.
5. Em **Networking**, gere um domínio público e atualize `APP_URL` com sua URL HTTPS final.
6. Em **Deploy > Healthcheck Path**, informe `/health`.

## Variáveis obrigatórias

Use os valores do seu projeto Supabase; não os registre em arquivos do repositório.

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:gere_uma_chave_laravel_unica
APP_URL=https://seu-dominio.up.railway.app

DB_CONNECTION=pgsql
DB_HOST=seu-host-do-supabase
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha
DB_SCHEMA=public

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
```

Para gerar `APP_KEY`, execute uma vez na pasta `casino` em uma máquina que tenha as dependências instaladas:

```powershell
php artisan key:generate --show
```

Copie apenas o resultado para a variável `APP_KEY` no Railway.

## Extensões PHP e banco

Adicione também esta variável no Railway para garantir suporte ao PostgreSQL e às imagens:

```dotenv
RAILPACK_PHP_EXTENSIONS=pdo_pgsql,pgsql,mbstring,gd
```

Após definir as variáveis, abra o terminal do serviço e rode a migração uma única vez:

```sh
cd casino && php artisan migrate --force
```

Não execute `migrate:fresh` em produção: ele apaga tabelas existentes.

## Processo recorrente

O fechamento diário de RevShare é um comando separado. Crie um segundo serviço/cron no Railway, usando o mesmo repositório e as mesmas variáveis, com o comando:

```sh
cd casino && php artisan affiliate:close-revshare
```

Agende-o uma vez por dia. O processo web não deve ser usado para tarefas recorrentes.
