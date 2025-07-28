# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions for PostgreSQL
RUN apt-get update && apt-get install -y \
  libpq-dev git unzip \
  && docker-php-ext-install pdo_pgsql

# Enable Apache mod_rewrite (for pretty URLs if you use them)
RUN a2enmod rewrite

# Copy app files to Apache's web root
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer and run install if composer.json exists
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Set Apache DocumentRoot to /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Update <Directory> in apache2.conf to point to public directory (for .htaccess, etc)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Set appropriate permissions for web files
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
