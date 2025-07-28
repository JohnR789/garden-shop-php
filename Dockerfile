# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
  libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libzip-dev unzip git \
  && docker-php-ext-install pdo_mysql

# Enable Apache mod_rewrite (for pretty URLs if you use them)
RUN a2enmod rewrite

# Copy app files to Apache's web root
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# If you use Composer, install it and run install
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Set appropriate permissions for web files
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
