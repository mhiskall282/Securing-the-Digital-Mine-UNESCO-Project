FROM php:8.2-cli-alpine

# Install fast pre-compiled extension installer helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system utilities & PostgreSQL extensions
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
    COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

# Copy full application source code (including pre-bundled vendor and build assets)
COPY . .

WORKDIR /var/www/html/dashboard

# Initialize production .env file from .env.example
RUN cp -n .env.example .env

# Pre-create Laravel framework storage directories & set permissions
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Outage-proof build step: if vendor already exists, dump autoload; otherwise fallback to composer install
RUN if [ -f "vendor/autoload.php" ]; then \
        echo "[+] Using pre-bundled vendor dependencies. Skipping external downloads." \
        && composer dump-autoload --optimize --no-dev; \
    else \
        composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader; \
    fi

# If pre-built frontend assets exist, skip npm build; otherwise run npm build
RUN if [ -d "public/build" ]; then \
        echo "[+] Using pre-compiled production assets."; \
    else \
        (npm ci || npm install) && npm run build; \
    fi

# Expose Render PORT
EXPOSE 8000

CMD ["sh", "-c", "cp -n .env.example .env; mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database; touch database/database.sqlite; chmod -R 777 storage bootstrap/cache database; php artisan migrate --force; php artisan db:seed --force; php artisan view:clear; php artisan serve --host=0.0.0.0 --port=$PORT"]
