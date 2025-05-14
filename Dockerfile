# Use a base image with Nginx and PHP. Adjust PHP version if needed.
# Check richarvey/nginx-php-fpm on Docker Hub for available tags/versions.
# For Laravel 9+ (PHP 8.0+), you might need a newer tag like '3.1.6' or 'latest'
FROM richarvey/nginx-php-fpm:latest

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Image environment variables
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel specific environment variables (can be overridden in Render's UI)
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root (Render's build environment might run as root)
ENV COMPOSER_ALLOW_SUPERUSER 1

# Expose port 80 for Nginx
EXPOSE 80

# The CMD instruction should be the last instruction in your Dockerfile.
# It specifies the command to run when the container starts.
# The base image richarvey/nginx-php-fpm has its own start script.
CMD ["/start.sh"]