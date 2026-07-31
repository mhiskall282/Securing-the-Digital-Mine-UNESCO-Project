FROM php:8.2-cli-alpine

# Install system dependencies & PostgreSQL extensions
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql pgsql

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
