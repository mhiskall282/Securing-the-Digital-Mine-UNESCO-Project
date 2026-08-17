FROM php:8.2-cli-alpine

# Install fast pre-compiled extension installer helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system utilities & PostgreSQL extensions rapidly
RUN apk add --no-cache git nodejs npm zip unzip curl \
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
    COMPOSER_MAX_PARALLEL_HTTP=4

WORKDIR /var/www/html/dashboard

# Copy dependency manifests first for Docker layer caching
COPY dashboard/composer.json dashboard/composer.lock* ./
COPY dashboard/package.json dashboard/package-lock.json* ./

# Configure Composer & install dependencies with resilient fallback/retry to handle transient GitHub rate limits (HTTP 429)
RUN composer config -g process-timeout 2000 \
    && composer config -g secure-http true \
    && ( \
        composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --no-progress || \
        (echo "[!] Retrying composer install with backoff..." && sleep 6 && composer install --no-dev --no-scripts --no-interaction --prefer-dist --no-progress) || \
        (echo "[!] Final retry with alternative sources..." && sleep 12 && composer install --no-dev --no-scripts --no-interaction --no-progress) \
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
