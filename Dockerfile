# -----------------------
# Stage 1: Composer Dependencies
# -----------------------
FROM composer:2 AS vendor

WORKDIR /app

# Copy only composer files first to leverage Docker layer caching
COPY composer.json composer.lock ./

# -----------------------
# Stage 2: PHP-FPM + App
# -----------------------
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies & PHP extensions in one layer
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libpq-dev \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy Composer from the vendor stage
COPY --from=vendor /usr/bin/composer /usr/bin/composer
#COPY --from=vendor /app/vendor /var/www/vendor

# Copy Laravel source code
COPY . /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

EXPOSE 9000

CMD ["php-fpm"]
