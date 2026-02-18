# PT Indoocean - Production Dockerfile
FROM php:8.1-apache

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
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd zip

# Enable Apache modules
RUN a2enmod rewrite headers ssl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Install PHP dependencies for recruitment module
WORKDIR /var/www/html/recruitment
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Install PHP dependencies for ERP module
WORKDIR /var/www/html/erp
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Back to root
WORKDIR /var/www/html

# Create required directories
RUN mkdir -p /var/www/html/uploads \
    && mkdir -p /var/www/html/erp/writable/logs \
    && mkdir -p /var/www/html/erp/writable/cache \
    && mkdir -p /var/www/html/erp/writable/session \
    && mkdir -p /var/www/html/recruitment/writable/logs \
    && mkdir -p /var/www/html/recruitment/writable/cache \
    && mkdir -p /var/www/html/recruitment/writable/session

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && chmod -R 775 /var/www/html/uploads \
    && chmod -R 775 /var/www/html/erp/writable \
    && chmod -R 775 /var/www/html/recruitment/writable \
    && find /var/www/html -name '.htaccess' -exec chmod 644 {} \;

# Configure Apache
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
