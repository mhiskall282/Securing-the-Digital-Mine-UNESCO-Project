FROM php:8.2-cli-alpine

# Install fast pre-compiled extension installer helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system utilities & PostgreSQL extensions rapidly
RUN apk add --no-cache git nodejs npm zip unzip \
    && install-php-extensions pdo_pgsql pgsql

# Copy Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP & Node dependencies and build production assets
RUN composer install --no-dev --no-scripts --no-interaction \
    && npm install \
    && npm run build

# Expose Render PORT
EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan view:clear && php artisan serve --host=0.0.0.0 --port=$PORT"]
