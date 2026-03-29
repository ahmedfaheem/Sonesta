# Use official PHP 8.4 FPM image
FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www

# -------------------------------
# Install system dependencies
# -------------------------------
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    unzip \
    git \
    curl \
    pkg-config \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# -------------------------------
# Install PHP extensions required by Laravel
# -------------------------------
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# -------------------------------
# Install Composer
# -------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# -------------------------------
# Copy full Laravel project
# -------------------------------
COPY . .

RUN mkdir -p bootstrap/cache storage \
    && chmod -R 777 bootstrap/cache storage

# -------------------------------
# Install PHP dependencies
# -------------------------------
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# -------------------------------
# Install Node.js (for frontend builds)
# -------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# -------------------------------
# Install Node.js dependencies
# -------------------------------
RUN npm install

# -------------------------------
# Ensure SQLite file and Laravel writable directories exist
# -------------------------------
RUN mkdir -p database && touch database/database.sqlite \
    && chown -R www-data:www-data /var/www/database /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/database /var/www/storage /var/www/bootstrap/cache

# -------------------------------
# Expose php-fpm port
# -------------------------------
EXPOSE 9000

# -------------------------------
# Start PHP-FPM for Nginx
# -------------------------------
CMD ["php-fpm"]
