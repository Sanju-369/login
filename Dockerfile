# Use official PHP-Apache image
FROM php:8.2-apache

# Install necessary PHP extensions & system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && apt-get clean

# Enable Apache mod_rewrite for pretty URLs
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy the app files to the container
COPY . .

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose the default Apache port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
