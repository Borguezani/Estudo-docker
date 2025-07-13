# 🚀 Projeto Laravel + React com Docker

Este projeto utiliza Laravel como backend e React como frontend, ambos rodando em containers Docker com PostgreSQL.

## 📋 Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- Git

## 🔧 Configuração Inicial

### 1. Clone o repositório

```bash
git clone https://github.com/Borguezani/Teste.git
cd Teste
```

### 2. Configure o arquivo .env do backend

```bash
# Navegue para a pasta backend
cd backend

# Copie o arquivo de exemplo
cp .env.example .env

# Volte para a raiz do projeto
cd ..
```

### 3. Inicie os containers

```bash
# Construa e inicie todos os containers
docker-compose up -d --build
```

> **Nota**: Na primeira execução, pode demorar alguns minutos para baixar e construir as imagens.

### 4. Gere a chave da aplicação Laravel e instale as dependencias

```bash
# Instale as dependencias
docker-compose exec backend composer install
# Gere a chave de criptografia do Laravel
docker-compose exec backend php artisan key:generate
```

### 5. Execute as migrações do banco de dados

```bash
# Execute as migrações para criar as tabelas
docker-compose exec backend php artisan migrate
```

## 🌐 Acessando a aplicação

Após a configuração, você pode acessar:

- **Frontend (React)**: [http://localhost:3000](http://localhost:3000)
- **Backend (Laravel)**: [http://localhost:8000](http://localhost:8000)
- **Banco PostgreSQL**: `localhost:5432`
  - Usuário: `user`
  - Senha: `my-secret-pw`
  - Database: `laravel`

## 🛠️ Comandos úteis

### Gerenciamento dos containers

```bash
# Iniciar os containers
docker-compose up -d

# Parar os containers
docker-compose down

# Reconstruir e iniciar
docker-compose up -d --build

# Ver logs em tempo real
docker-compose logs -f

# Ver logs de um serviço específico
docker-compose logs -f backend
```

### Comandos Laravel (dentro do container)

```bash
# Executar comandos Artisan
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan make:controller ExampleController
docker-compose exec backend php artisan route:list

# Limpar cache
docker-compose exec backend php artisan config:clear
docker-compose exec backend php artisan cache:clear

# Instalar dependências Composer
docker-compose exec backend composer install
docker-compose exec backend composer require package/name

# Acessar o container via bash
docker-compose exec backend bash
```

### Comandos React (dentro do container)

```bash
# Instalar dependências NPM
docker-compose exec frontend npm install

# Acessar o container via bash
docker-compose exec frontend sh
```

### Modificando o .env

Quando modificar o arquivo `.env`, execute:

```bash
# Limpe o cache de configuração
docker-compose exec backend php artisan config:clear
```

### Instalando novas dependências

**Para o Laravel:**
```bash
docker-compose exec backend composer require nome-do-pacote
```

**Para o React:**
```bash
docker-compose exec frontend npm install nome-do-pacote
```

## 🗃️ Banco de dados

### Configurações do PostgreSQL

As configurações do banco estão no arquivo `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=user
DB_PASSWORD=my-secret-pw
```

### Comandos úteis do banco

```bash
# Executar migrações
docker-compose exec backend php artisan migrate

# Reverter migrações
docker-compose exec backend php artisan migrate:rollback

# Status das migrações
docker-compose exec backend php artisan migrate:status

# Executar seeders
docker-compose exec backend php artisan db:seed
```

## 🚨 Solução de problemas

### Containers não iniciam

```bash
# Pare todos os containers
docker-compose down

# Remova containers antigos (se houver)
docker system prune -f

# Inicie novamente
docker-compose up -d --build
```

### Erro de permissões no Laravel

```bash
# Execute dentro do container
docker-compose exec backend chmod -R 775 storage bootstrap/cache
docker-compose exec backend chown -R www-data:www-data storage bootstrap/cache
```

### Erro "vendor/autoload.php not found"

Este erro acontece quando você tenta executar comandos `php artisan` diretamente no host. Use sempre:

```bash
# ❌ Errado (no host)
php artisan key:generate

# ✅ Correto (no container)
docker-compose exec backend php artisan key:generate
```

### Limpar tudo e recomeçar

```bash
# Pare e remova tudo
docker-compose down
docker system prune -f
docker volume prune -f

# Reconstrua tudo
docker-compose up -d --build
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate
```

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

**Desenvolvido com ❤️ usando Laravel, React e Docker**
