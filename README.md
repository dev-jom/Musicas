# Avaliacao de Musicas

Sistema pessoal para avaliar álbuns musicais importados do YouTube Music.

## Funcionalidades

- Importar álbuns via link do YouTube Music (scraper com yt-dlp)
- Avaliar cada faixa com nota (0–10) e extras exclusivos por álbum (melhor, favorita, menos gostei)
- Marcar faixas baixadas
- Comentário individual por faixa e texto geral do álbum
- Rascunho vs. publicado — só publicados aparecem na tela inicial
- Interface escura com Bootstrap 5

---

## Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) + [Docker Compose](https://docs.docker.com/compose/install/)
- [Laradock](https://laradock.io/) como ambiente de desenvolvimento
- Git

---

## Passo a passo para rodar localmente

### 1. Clonar o projeto

```bash
git clone https://github.com/dev-jom/Musicas.git
cd Musicas
```

### 2. Configurar o Laradock

Se ainda não tiver o Laradock, clone na pasta pai do projeto:

```bash
cd ..
git clone https://github.com/laradock/laradock.git
cd laradock
cp env-example .env
```

No `.env` do Laradock, ajuste:

```
POSTGRES_DB=musicas
POSTGRES_USER=default
POSTGRES_PASSWORD=secret
```

Suba os containers:

```bash
docker compose up -d nginx php-fpm postgres
```

### 3. Configurar o .env da aplicação

```bash
cp .env.example .env
```

Edite o `.env` com:

```env
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=musicas
DB_USERNAME=default
DB_PASSWORD=secret
```

> `DB_HOST=postgres` é o nome do container Docker. Fora do Docker use `127.0.0.1`.

### 4. Instalar dependências PHP

```bash
cd ../laradock
docker compose exec php-fpm bash -lc "cd /var/www/Musicas && composer install"
```

### 5. Gerar a chave da aplicação

```bash
docker compose exec php-fpm bash -lc "cd /var/www/Musicas && php artisan key:generate"
```

### 6. Rodar as migrations

```bash
docker compose exec php-fpm bash -lc "cd /var/www/Musicas && php artisan migrate"
```

### 7. Criar o link simbólico de storage (capas dos álbuns)

```bash
docker compose exec php-fpm bash -lc "cd /var/www/Musicas && php artisan storage:link"
```

### 8. Configurar o Python para importar álbuns

O scraper usa Python + yt-dlp. Instale dentro do container php-fpm:

```bash
docker compose exec php-fpm bash

# Dentro do container:
apt update && apt install -y python3.12-venv
cd /var/www/Musicas
python3 -m venv .venv
.venv/bin/python -m pip install -r scripts/requirements.txt
exit
```

Adicione ao `.env` do projeto:

```env
PYTHON_BIN=/var/www/Musicas/.venv/bin/python
```

> Ajuste o caminho caso o projeto esteja em outro local dentro do container.

### 9. Configurar o nginx (Laradock)

Crie o arquivo `laradock/nginx/sites/musicas.conf`:

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/Musicas/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass php-fpm:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

Reinicie o nginx:

```bash
docker compose restart nginx
```

### 10. Acessar a aplicação

Abra: **http://localhost**

---

## Estrutura resumida

```
app/Http/Controllers/AlbumReviewController.php  — lógica principal
app/Models/Album.php                             — model de álbum
app/Models/Track.php                             — model de faixa
scripts/ytmusic_album_scraper.py                 — scraper Python (yt-dlp)
scripts/requirements.txt                         — dependências Python
resources/views/albums/index.blade.php           — tela inicial (grid de álbuns)
resources/views/albums/drafts.blade.php          — rascunhos
resources/views/albums/show.blade.php            — avaliação/edição do álbum
```

---

## Variáveis de ambiente relevantes

| Variável | Descrição | Exemplo |
|---|---|---|
| `DB_CONNECTION` | Driver do banco | `pgsql` |
| `DB_HOST` | Host do banco | `postgres` (Docker) |
| `DB_DATABASE` | Nome do banco | `musicas` |
| `DB_USERNAME` | Usuário | `default` |
| `DB_PASSWORD` | Senha | `secret` |
| `PYTHON_BIN` | Caminho do Python com yt-dlp | `/var/www/Musicas/.venv/bin/python` |
