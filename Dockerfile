# Dockerfile for Laravel on Render with PostgreSQL and Vite

# Use a base image with Nginx and PHP.
# Ensure this tag provides PHP 8.0+ for Laravel 9+ (e.g., richarvey/nginx-php-fpm:3.1.6 or newer)
FROM richarvey/nginx-php-fpm:latest

# Install system dependencies:
# - nodejs & npm: for building frontend assets
# - postgresql-dev: for compiling the pdo_pgsql PHP extension
RUN apk add --no-cache nodejs npm postgresql-dev

# Install required PHP extensions:
# - pdo_pgsql: for PostgreSQL database connectivity
# - zip: often required by composer packages
# The base image (richarvey/nginx-php-fpm) likely includes many common Laravel extensions
# (e.g., mbstring, openssl, tokenizer, xml, dom, fileinfo). Add what\\'s missing.
RUN docker-php-ext-install pdo_pgsql zip

# Set working directory
WORKDIR /var/www/html

# --- PHP Dependencies ---
# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install PHP dependencies for production
# COMPOSER_ALLOW_SUPERUSER=1 is set as an ENV var later, but it\\'s effective for RUN commands too.
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# --- Node.js Dependencies & Frontend Build ---
# Copy package manager files
COPY package.json package-lock.json ./
# If you use yarn, copy yarn.lock and use yarn commands

# Install Node.js dependencies
RUN npm install

# Copy the rest of the application files
# This includes your .env.example (though Render uses its own env var system),
# config files, routes, resources, etc.
COPY . .

# Build Vite assets for production
# This command should compile your JS/CSS into the public/build directory
RUN npm run build

# --- Laravel Optimization (run after all files are present) ---
# These commands improve performance in production.
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# --- Environment Variables ---
# WEBROOT tells the base image (richarvey/nginx-php-fpm) where your public directory is.
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1       # Send PHP errors to stderr for easier debugging on Render
ENV RUN_SCRIPTS 1             # Enable run_scripts in /etc/cont-init.d for richarvey/nginx-php-fpm
ENV REAL_IP_HEADER 1

# Laravel specific environment variables
# These will be set in Render\\'s UI, but defaults are good for the image.
ENV APP_ENV production
ENV APP_DEBUG false           # Should always be false in production
ENV LOG_CHANNEL stderr        # Recommended for containerized environments
ENV DB_CONNECTION pgsql       # Default to PostgreSQL for Render

# Set SKIP_COMPOSER to 1 because we\\'ve already run composer install during the build.
# This prevents the base image\\'s init script from trying to run it again at container startup.
ENV SKIP_COMPOSER 1

# Allow composer to run as root. This is sometimes needed if build steps or base image scripts run as root.
ENV COMPOSER_ALLOW_SUPERUSER 1

# --- Permissions ---
# The richarvey/nginx-php-fpm image typically handles permissions for /var/www/html
# and runs PHP-FPM/Nginx as www-data. If you encounter permission issues with
# storage/ or bootstrap/cache/, you might need to uncomment and adjust the following:
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \\
#     chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 for Nginx (Render will map this to 80/443 externally)
EXPOSE 80

# The CMD instruction specifies the command to run when the container starts.
# The base image richarvey/nginx-php-fpm provides /start.sh which handles starting Nginx & PHP-FPM.
CMD ["/start.sh"]
