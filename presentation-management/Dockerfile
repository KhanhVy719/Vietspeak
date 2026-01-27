FROM php:8.2-fpm-bullseye

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libgmp-dev \
    libyaml-dev

# Use mlocati/php-extension-installer to install extensions easily
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install PHP extensions including Cassandra (stable) and Redis
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gmp \
    redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Remove composer.lock to force re-resolution for PHP 8.2
RUN rm -f /var/www/composer.lock

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader

# Change current user to www
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]
