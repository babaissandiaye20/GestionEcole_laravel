# Utilise l'image officielle PHP avec FPM (FastCGI Process Manager)
FROM php:8.1.2-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    git \
    unzip \
    libpq-dev \
    vim \
    cron \
    supervisor

# Installation des extensions PHP requises
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Définition du répertoire de travail
WORKDIR /var/www/html

# Copier le contenu du projet Laravel dans le conteneur
COPY . /var/www/html/

# Attribution des permissions au dossier de stockage et au cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Set the correct permissions for the storage directory
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage

# Générer les clés Laravel
RUN php artisan key:generate

# Configuration du cache et des migrations lors du build
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Exposer le port utilisé par PHP-FPM
EXPOSE 9000

# Commande pour démarrer PHP-FPM lors de l'initialisation du conteneur
CMD ["php-fpm"]
