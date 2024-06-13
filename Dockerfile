FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    libc-client-dev \
    libfreetype6-dev \
    libmcrypt-dev \
    libpng-dev \
    libjpeg-dev \
    libldap-common \
    libldap2-dev \
    zlib1g-dev \
    libkrb5-dev \
    libtidy-dev \
    libzip-dev \
    libsodium-dev \
    libpq-dev \
    libonig-dev \
    netcat-openbsd && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr \
    && docker-php-ext-install gd mysqli mbstring pgsql pdo pdo_mysql pdo_pgsql opcache zip iconv tidy \
    && docker-php-ext-configure ldap --with-libdir=lib/$(gcc -dumpmachine)/ \
    && docker-php-ext-install ldap \
    && docker-php-ext-configure imap --with-imap-ssl --with-kerberos \
    && docker-php-ext-install imap \
    && docker-php-ext-install sodium \
    && pecl install mcrypt-1.0.6 \
    && docker-php-ext-enable mcrypt \
    && docker-php-ext-install exif

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set recommended PHP.ini settings
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Set PHP defaults for LimeSurvey
RUN { \
    echo 'memory_limit=256M'; \
    echo 'upload_max_filesize=128M'; \
    echo 'post_max_size=128M'; \
    echo 'max_execution_time=120'; \
    echo 'max_input_vars=10000'; \
    echo 'date.timezone=UTC'; \
    echo 'session.gc_maxlifetime=86400'; \
    echo 'session.save_path="/var/lib/php/sessions"'; \
    } > /usr/local/etc/php/conf.d/limesurvey.ini

# Copy LimeSurvey files into the container
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/ && \
    chmod -R ug=rx /var/www/html && \
    mkdir -p /var/www/html/tmp/runtime && \
    chown -R www-data:www-data /var/www/html/tmp/runtime && \
    chmod -R 775 /var/www/html/tmp/runtime

# Create the session save path directory and set permissions
RUN mkdir -p /var/lib/php/sessions && \
    chown -R www-data:www-data /var/lib/php/sessions && \
    chmod -R 775 /var/lib/php/sessions

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install LimeSurvey dependencies
WORKDIR /var/www/html
RUN composer install --no-interaction --no-plugins --no-scripts

# Expose port
EXPOSE 80

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN ln -s /usr/local/bin/docker-entrypoint.sh /entrypoint.sh # backwards compat

# ENTRYPOINT resets CMD
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
