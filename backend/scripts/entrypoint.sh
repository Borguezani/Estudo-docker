#!/bin/bash

# Aguardar o banco de dados
echo "Aguardando o banco de dados..."
until pg_isready -h db -p 5432 -U user -d laravel; do
  echo "Banco de dados não está pronto. Aguardando..."
  sleep 1
done
echo "Banco de dados está pronto!"

# Executar migrações (se necessário)
php artisan migrate --force

# Publicar configuração JWT
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

# Gerar JWT secret se não existir
if [ -z "$JWT_SECRET" ]; then
    php artisan jwt:secret
fi

# Limpar caches
php artisan config:clear
php artisan cache:clear

# Iniciar supervisor (que gerencia nginx e php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf