FROM php:8.2-cli-alpine

# Install fast pre-compiled extension installer helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system utilities, Git (for direct git clone) & PostgreSQL extensions
RUN apk add --no-cache git nodejs npm zip unzip curl openssh-client \
    && install-php-extensions pdo_pgsql pgsql

# Copy Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set Production Environment Variables
ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_KEY=base64:UzNjdXJpbmc1VGhlMURpZ2l0YWwxTWluZTJTVUlURT0= \
    SESSION_DRIVER=cookie \
    CACHE_STORE=file \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_PROCESS_TIMEOUT=2000 \
    COMPOSER_NO_INTERACTION=1

WORKDIR /var/www/html/dashboard

# Copy dependency manifests first for Docker layer caching
COPY dashboard/composer.json dashboard/composer.lock* ./
COPY dashboard/package.json dashboard/package-lock.json* ./

# Install dependencies using --prefer-source (git clones) to completely bypass GitHub zip (codeload) HTTP 429 rate limit
RUN composer config -g preferred-install auto \
    && composer config -g process-timeout 2000 \
    && ( \
        composer install --no-dev --no-scripts --no-interaction --prefer-source --optimize-autoloader --no-progress || \
        composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --no-progress \
    ) \
    && (npm ci || npm install)

# Copy full application source code
WORKDIR /var/www/html
COPY . .

WORKDIR /var/www/html/dashboard

# Initialize production .env file from .env.example
RUN cp -n .env.example .env

# Pre-create Laravel framework storage directories & set permissions
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Build production frontend assets and dump optimized autoloader
RUN npm run build \
    && composer dump-autoload --optimize --no-dev

# Expose Render PORT
EXPOSE 8000

CMD ["sh", "-c", "cp -n .env.example .env; mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database; touch database/database.sqlite; chmod -R 777 storage bootstrap/cache database; php artisan migrate --force; php artisan db:seed --force; php artisan view:clear; php artisan serve --host=0.0.0.0 --port=$PORT"]
