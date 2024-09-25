# Use the official PHP image with FPM (FastCGI Process Manager)
FROM php:8.1.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    curl \
    git \
    unzip \
    libpq-dev \
    vim \
    cron \
    supervisor

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd
RUN docker-php-ext-install zip

# Set the working directory
WORKDIR /var/www/html

# Copy the Laravel project files into the container
COPY . /var/www/html/

# Copy swagger.json file into the container
COPY ./public/swagger.json /var/www/html/public/swagger.json

# Set permissions for storage, cache, and swagger.json
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/swagger.json \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod 644 /var/www/html/public/swagger.json

# Generate Laravel application key
RUN php artisan key:generate

# Configure cache and run migrations during the build
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose the port used by PHP-FPM
EXPOSE 9000

# Command to start PHP-FPM when the container starts
CMD ["php-fpm"]
