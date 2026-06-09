# Avaliação de Músicas

Sistema pessoal para avaliar álbuns musicais importados do YouTube Music via scraper (yt-dlp).

## 🚀 Guia de Instalação (Laradock)

Siga estes passos para rodar o projeto em qualquer máquina usando Docker + Laradock.

### 1. Preparar o ambiente Laradock
Certifique-se de ter o [Laradock](https://laradock.io/) configurado na pasta pai do projeto e os containers `nginx`, `php-fpm`, `postgres` (ou `mysql`) e `workspace` rodando.

### 2. Configuração Inicial do Laravel
Na pasta raiz do projeto (`Musicas`), execute:

```bash
# Copiar o ambiente
cp .env.example .env

# Instalar dependências PHP (via Workspace)
docker compose exec workspace composer install

# Gerar chave e criar link de storage
docker compose exec workspace php artisan key:generate
docker compose exec workspace php artisan storage:link
```

### 3. Configurar o Scraper Python (Passo Crucial)
O scraper roda via PHP no container `php-fpm`. Por isso, as dependências do Python **devem** ser instaladas lá dentro.

```bash
# 1. Entrar no container php-fpm
sudo docker compose exec php-fpm bash

# 2. Atualizar e instalar suporte ao Python (dentro do container)
apt update && apt install -y python3-venv python3-pip

# 3. Criar o ambiente virtual e instalar o yt-dlp
cd /var/www/Musicas
python3 -m venv .venv
.venv/bin/python -m pip install yt-dlp

# 4. Ajustar permissões para o servidor web
chown -R www-data:www-data .venv
chmod -R 755 .venv
exit
```

### 4. Configurar Variáveis de Ambiente (.env)
Edite o arquivo `.env` do projeto para apontar para o binário do Python correto:

```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=musicas
DB_USERNAME=default
DB_PASSWORD=secret

# Caminho absoluto dentro do container php-fpm
PYTHON_BIN=/var/www/Musicas/.venv/bin/python
```

### 5. Banco de Dados
Rode as migrations para criar as tabelas:
```bash
sudo docker compose exec workspace php artisan migrate
```

---

## 🛠 Solução de Problemas Comuns

### Erro: "Dependência Python ausente: yt-dlp"
Isso acontece se o `php-fpm` não conseguir executar o Python ou se o módulo não estiver instalado.
- Verifique se o caminho em `PYTHON_BIN` no `.env` está correto.
- Certifique-se de que rodou o `pip install yt-dlp` de dentro do container `php-fpm`.
- Rode `chmod -R 755 .venv` para garantir que o usuário `www-data` tem acesso.

### Erro ao importar: "Link Inválido"
O scraper suporta URLs de álbuns ou playlists do YouTube Music (ex: `https://music.youtube.com/playlist?list=...`). Links de vídeos comuns do YouTube podem não funcionar conforme esperado.

---

## 📂 Estrutura do Projeto
- `app/Http/Controllers/AlbumReviewController.php`: Lógica de importação e avaliação.
- `scripts/ytmusic_album_scraper.py`: Script Python que usa `yt-dlp` para extrair metadados.
- `resources/views/albums/`: Telas do sistema.
