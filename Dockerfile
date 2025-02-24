# Use an official PHP runtime as a parent image
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y unzip git

# Enable required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy the project files into the container
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create the service account JSON file securely
RUN echo "$GOOGLE_APPLICATION_CREDENTIALS_JSON" > /var/www/html/service-account.json && \
    chmod 600 /var/www/html/service-account.json

# Set environment variable for Google API client
ENV GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/service-account.json

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
