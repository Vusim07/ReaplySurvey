FROM php:apache

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libicu-dev \
    libxml2-dev \
    libpq-dev

# Enable necessary PHP extensions
RUN docker-php-ext-install pdo_pgsql gd intl soap

# Copy LimeSurvey files into the container
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/

# Expose port
EXPOSE 80
