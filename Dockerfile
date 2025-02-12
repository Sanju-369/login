# Use official PHP image with Apache
FROM php:8.0-apache

# Install required extensions including pdo_mysql
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Copy project files to the container
COPY . /var/www/html/

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html

# Expose the web server port
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
