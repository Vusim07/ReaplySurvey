FROM php:apache

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libicu-dev \
    libxml2-dev \
    libpq-dev \
    curl \
    libzip-dev

# Enable necessary PHP extensions
RUN docker-php-ext-install pdo_pgsql gd intl soap zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

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
