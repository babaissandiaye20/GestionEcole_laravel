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
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install project dependencies
RUN composer install --no-autoloader --no-scripts --no-interaction

# Copy the rest of the application code
COPY . .

# Generate the autoloader
RUN composer dump-autoload

# Ensure .env file is copied (if it doesn't exist, copy .env.example)
COPY .env* .env

# Copy swagger.json file into the container
COPY ./public/swagger.json ./public/swagger.json

# Set permissions for storage, cache, swagger.json, and .env
RUN chown -R www-data:www-data storage bootstrap/cache public/swagger.json .env \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 644 public/swagger.json .env

# Generate Laravel application key
RUN php artisan key:generate --force

# Configure cache
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose the port used by PHP-FPM
EXPOSE 9000

# Command to start PHP-FPM when the container starts
CMD ["php-fpm"]
