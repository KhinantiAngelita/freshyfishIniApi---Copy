FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install dependencies
RUN composer install --no-scripts --no-interaction --prefer-dist

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Configure Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
RUN a2ensite 000-default.conf

# Set environment variables for MySQL connection
ENV DB_CONNECTION=mysql
ENV DB_HOST=po8o0oggc48888gccck0go4w
ENV DB_PORT=3306
ENV DB_DATABASE=default
ENV DB_USERNAME=mysql
ENV DB_PASSWORD=MOfpJB30KYXnQHoWqpwWUV1Ung43ucwqnQIAThcuWMIHJCb3AUxCIE76J0Yp4vby

# Expose port
EXPOSE 80

# Start server
CMD ["apache2-foreground"]