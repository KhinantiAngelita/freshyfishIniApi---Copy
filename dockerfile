# Use the official PHP image as a base image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-scripts --no-interaction --prefer-dist

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Install Nginx
RUN apt-get update && apt-get install -y nginx

# Copy Nginx configuration file
COPY default.conf /etc/nginx/conf.d/default.conf

# Expose port 80
EXPOSE 80

# Start Nginx and PHP-FPM
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]

# Set environment variables for MySQL connection
ENV DB_CONNECTION=mysql
ENV DB_HOST=po8o0oggc48888gccck0go4w
ENV DB_PORT=3306
ENV DB_DATABASE=default
ENV DB_USERNAME=mysql
ENV DB_PASSWORD=MOfpJB30KYXnQHoWqpwWUV1Ung43ucwqnQIAThcuWMIHJCb3AUxCIE76J0Yp4vby