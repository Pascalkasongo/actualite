FROM php:8.2-cli

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev

# Installer extensions PHP
RUN docker-php-ext-install zip gd pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /app

# Copier le projet
COPY . .

# Installer dépendances Symfony
RUN composer install --optimize-autoloader --no-interaction

# Lancer serveur
CMD php -S 0.0.0.0:8000 -t public