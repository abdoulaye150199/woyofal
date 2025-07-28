FROM php:8.2-fpm

# Installation des dépendances système avec retry
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        curl \
    && docker-php-ext-install pdo pdo_pgsql

# Installation manuelle de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

# Configuration du répertoire de travail
WORKDIR /var/www/html

# Configuration Git pour le répertoire
RUN git config --global --add safe.directory /var/www/html \
    && git config --global safe.directory '*'

# Copie des fichiers du projet
COPY . .

# Installation des dépendances PHP avec retry
RUN composer clear-cache \
    && composer install --no-interaction --optimize-autoloader --no-scripts \
    || (rm -rf vendor && composer install --no-interaction --optimize-autoloader --no-scripts)

# Permissions pour le répertoire de l'application
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/public

# Exposition du port PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]