# Use the official PHP image with Nginx as a base image
FROM serversideup/php:8.2-fpm-nginx

# Enable PHP OPcache
ENV PHP_OPCACHE_ENABLE=1

# Set working directory
WORKDIR /var/www/html

# Switch to root user to install dependencies
USER root

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

# Copy existing application directory contents with appropriate ownership
COPY --chown=www-data:www-data . /var/www/html

# Copy custom Nginx configuration files
COPY nginx.conf /etc/nginx/nginx.conf
COPY fastcgi-php.conf /etc/nginx/snippets/fastcgi-php.conf
COPY fastcgi.conf /etc/nginx/fastcgi.conf

# Switch to www-data user
USER www-data

# Install Node.js dependencies and build assets
RUN npm install
RUN npm run build

# Install Composer dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Nginx and PHP-FPM
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]