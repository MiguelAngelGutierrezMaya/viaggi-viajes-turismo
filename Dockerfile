# Use the official PHP 7.3 image
FROM php:7.3-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set the working directory
WORKDIR /var/www/html

# Copy the CodeIgniter project files to the container
COPY . /var/www/html

# Set permissions for the web server
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80