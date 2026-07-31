FROM php:8.2-cli-alpine

# Install fast pre-compiled extension installer helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system utilities & PostgreSQL extensions rapidly
RUN apk add --no-cache git nodejs npm zip unzip \
    && install-php-extensions pdo_pgsql pgsql

# Copy Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set Production Environment Variables
ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_KEY=base64:UzNjdXJpbmc1VGhlMURpZ2l0YWwxTWluZTJTVUlURT0= \
    SESSION_DRIVER=cookie \
    CACHE_STORE=file

WORKDIR /var/www/html

# Copy repository root files
COPY . .

# Set working directory to dashboard folder where composer.json & artisan exist
WORKDIR /var/www/html/dashboard

# Pre-create Laravel framework storage directories & set permissions
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Install PHP & Node dependencies and build production assets
RUN composer install --no-dev --no-scripts --no-interaction \
    && npm install \
    && npm run build

# Expose Render PORT
EXPOSE 8000

CMD ["sh", "-c", "mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache && chmod -R 777 storage bootstrap/cache && php artisan key:generate --force && php artisan migrate --force && php artisan view:clear && php artisan serve --host=0.0.0.0 --port=$PORT"]
