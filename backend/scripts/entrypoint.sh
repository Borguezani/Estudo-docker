#!/bin/bash

# Instalar dependências do Composer
composer install

# Verificar se o .env existe, se não, copiar do .env.example
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Gerar chave da aplicação apenas se não existir
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate
fi

# Executar migrações
php artisan migrate --force

# Iniciar o servidor
php artisan serve --host=0.0.0.0 --port=80