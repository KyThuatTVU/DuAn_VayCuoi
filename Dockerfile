FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/html

# Install PHP dependencies (if any)
# RUN composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
RUN npm install
RUN npx tailwindcss -i ./assets/css/tailwind.css -o ./assets/css/tailwind.output.css --minify || true

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Enable environment variables to be passed to PHP
RUN echo 'PassEnv DB_HOST DB_USER DB_PASS DB_NAME DB_CHARSET SITE_NAME SITE_URL ADMIN_EMAIL' >> /etc/apache2/conf-available/environment.conf && \
    echo 'PassEnv SESSION_LIFETIME PASSWORD_MIN_LENGTH UPLOAD_MAX_SIZE UPLOAD_PATH ALLOWED_EXTENSIONS' >> /etc/apache2/conf-available/environment.conf && \
    echo 'PassEnv GOOGLE_CLIENT_ID GOOGLE_CLIENT_SECRET GOOGLE_REDIRECT_URI' >> /etc/apache2/conf-available/environment.conf && \
    echo 'PassEnv MOMO_PARTNER_CODE MOMO_ACCESS_KEY MOMO_SECRET_KEY MOMO_ENDPOINT MOMO_REDIRECT_URL MOMO_IPN_URL' >> /etc/apache2/conf-available/environment.conf && \
    echo 'PassEnv SMTP_HOST SMTP_PORT SMTP_USER SMTP_PASS SMTP_FROM_EMAIL SMTP_FROM_NAME' >> /etc/apache2/conf-available/environment.conf && \
    echo 'PassEnv GROQ_API_KEY GROQ_MODEL' >> /etc/apache2/conf-available/environment.conf && \
    a2enconf environment

# Configure Apache
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]