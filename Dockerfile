FROM php:apache

# Install the PHP extensions we need
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
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr \
    && docker-php-ext-install gd mysqli mbstring pgsql pdo pdo_mysql pdo_pgsql opcache zip iconv tidy \
    && docker-php-ext-configure ldap --with-libdir=lib/$(gcc -dumpmachine)/ \
    && docker-php-ext-install ldap \
    && docker-php-ext-configure imap --with-imap-ssl --with-kerberos \
    && docker-php-ext-install imap \
    && docker-php-ext-install sodium exif \
    && pecl install mcrypt-1.0.6 \
    && docker-php-ext-enable mcrypt

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

# Set LimeSurvey-specific PHP settings
RUN { \
    echo 'memory_limit=256M'; \
    echo 'upload_max_filesize=128M'; \
    echo 'post_max_size=128M'; \
    echo 'max_execution_time=120'; \
    echo 'max_input_vars=10000'; \
    echo 'date.timezone=UTC'; \
    echo 'session.gc_maxlifetime=86400'; \
    echo 'session.save_path="/var/lime/sessions"'; \
    } > /usr/local/etc/php/conf.d/limesurvey.ini

# Create and set permissions for directories
RUN set -x; \
    chown -R www-data:www-data /var/www/html; \
    chmod -R ug=rx /var/www/html; \
    mkdir -p /var/lime/application/config /var/lime/upload /var/lime/plugins /var/lime/sessions /var/www/html/tmp/runtime; \
    chown -R www-data:www-data /var/lime/sessions /var/lime/application /var/lime/plugins /var/lime/upload /var/www/html/tmp/runtime; \
    chmod -R 775 /var/www/html/tmp/runtime; \
    cp -dpR /var/www/html/application/config/* /var/lime/application/config; \
    cp -dpR /var/www/html/upload/* /var/lime/upload; \
    cp -dpR /var/www/html/plugins/* /var/lime/plugins

# Define volumes
# VOLUME ["/var/www/html/plugins", "/var/www/html/upload", "/var/lime/sessions", "/var/www/html/application/config"]

# Copy LimeSurvey files into the container
COPY --chown=www-data:www-data . /var/www/html/

# Expose port 80
EXPOSE 80

# Set the working directory
WORKDIR /var/www/html
