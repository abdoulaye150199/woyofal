FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Copy .env file
COPY .env.example .env

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy project files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod 664 .env

# Configure PHP
RUN mkdir -p /usr/local/etc/php/conf.d && \
    echo "date.timezone = UTC" > /usr/local/etc/php/conf.d/app.ini && \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/app.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/app.ini

EXPOSE 8081

CMD ["php", "-S", "0.0.0.0:8081", "-t", "public/"]