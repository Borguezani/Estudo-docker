#!/bin/bash

# Executar migrações (se necessário)
php artisan migrate --force

# Iniciar o servidor Laravel
php artisan serve --host=0.0.0.0 --port=80