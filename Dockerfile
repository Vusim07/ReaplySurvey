FROM php:apache

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libicu-dev \
    libxml2-dev \
    libpq-dev \
    curl

# Enable necessary PHP extensions
RUN docker-php-ext-install pdo_pgsql gd intl soap

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy LimeSurvey files into the container
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/

# Change working directory
WORKDIR /var/www/html

# Install LimeSurvey dependencies
RUN composer install --no-interaction --no-plugins --no-scripts

# Expose port
EXPOSE 80
