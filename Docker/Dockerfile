FROM php:8.3-fpm

# Install PHP extensions and system dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    libicu-dev \
    libxslt1-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl xsl opcache mysqli soap xml sockets \
    && docker-php-ext-enable opcache

# Copy custom PHP configuration
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Add entrypoint for .env.local and composer install
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
