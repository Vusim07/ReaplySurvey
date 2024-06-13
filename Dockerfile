FROM php:apache

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libicu-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    libldap2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libpng-dev \
    libxpm-dev \
    libvpx-dev \
    curl

# Enable necessary PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm --with-vpx && \
    docker-php-ext-install pdo_pgsql gd intl soap zip ldap

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create the session save path directory and set permissions
RUN mkdir -p /var/lib/php/sessions && \
    chown -R www-data:www-data /var/lib/php/sessions && \
    chmod -R 775 /var/lib/php/sessions

# Set the session.save_path in PHP configuration
RUN echo "session.save_path = '/var/lib/php/sessions'" >> /usr/local/etc/php/conf.d/session.ini

# Copy LimeSurvey files into the container
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/

# Create and set permissions for the runtime directory
RUN mkdir -p /var/www/html/tmp/runtime && \
    chown -R www-data:www-data /var/www/html/tmp/runtime && \
    chmod -R 775 /var/www/html/tmp/runtime

# Change working directory
WORKDIR /var/www/html

# Install LimeSurvey dependencies
RUN composer install --no-interaction --no-plugins --no-scripts

# Expose port
EXPOSE 80
