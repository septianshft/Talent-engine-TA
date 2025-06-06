#!/usr/bin/env bash
# Exit on error
set -e

echo "Running composer"
# Install composer dependencies for production, without dev dependencies
# The --working-dir=/var/www/html is important because our Dockerfile sets this as the WORKDIR
composer install --no-dev --no-interaction --no-plugins --no-scripts --prefer-dist --optimize-autoloader --working-dir=/var/www/html

# Copy .env.example to .env if .env does not exist (Render will inject actual .env values)
# This is more of a fallback or for ensuring all keys are present if Render's .env is partial
# However, Render's environment variable management is the primary way to set these.
# Consider if this step is truly needed if all env vars are set in Render's UI.
# if [ ! -f /var/www/html/.env ]; then
#  cp /var/www/html/.env.example /var/www/html/.env
# fi

# Generate app key if it's not set (Render should set APP_KEY via environment variables)
# php artisan key:generate --force

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

# echo "Caching views..." # Optional: uncomment if you want to cache views
# php artisan view:cache

echo "Running migrations..."
# The --force flag is important for running migrations in production non-interactively
php artisan migrate --force

# Optional: Seed database if needed for demo (remove for actual production if seeding is large or one-time)
# echo "Seeding database..."
# php artisan db:seed --force

# Optional: Link storage
# if [ ! -L /var/www/html/public/storage ]; then
#    echo "Linking storage directory..."
#    php artisan storage:link
# fi

# Optional: Build frontend assets if not already built and committed
# echo "Building frontend assets..."
# npm install
# npm run build

echo "Deployment script finished successfully!"
