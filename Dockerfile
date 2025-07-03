FROM php:8.3.3-fpm

ENV COMPOSER_PROCESS_TIMEOUT=600
ENV REBUILD_DB=1

WORKDIR /var/www

COPY composer.* /var/www/

RUN apt-get update && apt-get install -y \
    unzip \
    nodejs \
    npm \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libxml2-dev \
    libzip-dev \
    libc-dev \
    wget \
    zlib1g-dev \
    zip \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql soap zip iconv bcmath \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-install sockets \
    && docker-php-ext-install exif \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install pcntl

RUN mkdir -p /etc/pki/tls/certs && \
    ln -s /etc/ssl/certs/ca-certificates.crt /etc/pki/tls/certs/ca-bundle.crt

# Install Redis extension
RUN wget -O redis-5.3.7.tgz 'http://pecl.php.net/get/redis-5.3.7.tgz' \
    && pecl install redis-5.3.7.tgz \
    && rm -rf redis-5.3.7.tgz \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Send update for php.ini
# COPY ./docker/php.development.ini /usr/local/etc/php/php.ini
RUN echo 'memory_limit = 256M\n\
max_execution_time = 300\n\
upload_max_filesize = 50M\n\
post_max_size = 50M\n\
opcache.enable = 1\n\
opcache.memory_consumption = 128\n\
opcache.max_accelerated_files = 10000\n\
opcache.revalidate_freq = 2\n\
opcache.save_comments = 1\n\
opcache.enable_file_override = 0\n\
realpath_cache_size = 10M\n\
realpath_cache_ttl = 120' > /usr/local/etc/php/conf.d/production.ini

# Install FrankenPHP
# RUN curl https://frankenphp.dev/install.sh | sh \
#     && mv frankenphp /usr/local/bin/frankenphp \
#     && chmod +x /usr/local/bin/frankenphp
RUN curl -L https://github.com/dunglas/frankenphp/releases/latest/download/frankenphp-linux-x86_64 -o /usr/local/bin/frankenphp \
    && chmod +x /usr/local/bin/frankenphp

# Copy the application
COPY . /var/www
COPY ./docker/frankenphp.json /etc/frankenphp/Caddyfile.json

# Composer & laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && php artisan octane:install \
    && php artisan storage:link \
    && php artisan optimize:clear \
    && php artisan optimize \
    && php artisan config:clear \
    && chmod -R 777 storage bootstrap/cache \
    && chown -R www-data:www-data storage \
    && composer dumpautoload

# Generate Swagger
RUN php artisan l5-swagger:generate

# Starts both, laravel server and job queue
CMD ["/var/www/docker/start.sh"]

# Expose port
EXPOSE 8100