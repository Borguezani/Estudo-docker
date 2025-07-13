FROM php:8.3.2-fpm

# Instalando dependências
RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
            curl \
            vim \
            openssl \
            libmemcached-dev \
            libz-dev \
            zlib1g-dev \
            libzip-dev \
            zip \
            unzip \
            libpq-dev \
            libjpeg-dev \
            libpng-dev \
            libxml2-dev \
            libfreetype6-dev \
            libssl-dev \
            libmcrypt-dev \
            libonig-dev \
            libgmp-dev \
            npm \
            nodejs

# Limpeza do cache do apt
RUN rm -rf /var/lib/apt/lists/*

# Instalando extensões PHP
RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install gmp \
    && docker-php-ext-install ctype \
    && docker-php-ext-install zip \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install xml

RUN docker-php-ext-configure gd \
            --prefix=/usr \
            --with-jpeg \
            --with-freetype \
    && docker-php-ext-install gd \
    && php -r 'var_dump(gd_info());'

# Instalando Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer self-update 2.6.6

# Diretório de trabalho e cópia do código-fonte
WORKDIR /app
COPY . .

# Criando diretórios necessários e definindo permissões
RUN mkdir -p /app/bootstrap/cache \
    && mkdir -p /app/storage/logs \
    && mkdir -p /app/storage/framework/cache \
    && mkdir -p /app/storage/framework/sessions \
    && mkdir -p /app/storage/framework/views \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache

# Instalando dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Criando o arquivo .env apenas se não existir (fallback para .env.example)
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Gerando a chave da aplicação Laravel apenas se APP_KEY estiver vazio
RUN if ! grep -q "APP_KEY=base64:" .env; then php artisan key:generate; fi

# Definindo permissões adequadas
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage \
    && chmod -R 755 /app/bootstrap/cache

# Expondo a porta 80 (se necessário)
EXPOSE 80