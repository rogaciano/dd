FROM php:8.3-apache

# Update and install system packages
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    curl \
    git \
 && rm -rf /var/lib/apt/lists/*

# Install PHP extensions: pdo_pgsql for postgres, pcntl for horizon, zip for composer
RUN docker-php-ext-install pdo_pgsql pcntl zip

# Install Redis extension
RUN pecl install redis \
 && docker-php-ext-enable redis

# Optional: Install Node.js just in case
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
 && apt-get install -y nodejs

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Setup VirtualHost pointing to Laravel's public folder
COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

# Install global Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Switch user setup is generally a good idea, but for local dev with bind mounts, 
# www-data or root can be used. We'll run as root for simplicity with permissions in dev.
WORKDIR /var/www/html
