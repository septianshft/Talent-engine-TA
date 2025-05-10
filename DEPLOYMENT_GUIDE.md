# Free Deployment Guide

## Architecture
```
[User Browser] <-> [Vercel/GH Pages] <-> [Heroku PHP Dyno] <-> [ElephantSQL]
                     |                     |
                     |-> [Cloudinary CDN]<-|
```

## 1. Docker Setup
```dockerfile:Dockerfile
# Base PHP image
FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    libzip-dev \
    unzip \
    git \
    nginx

# Configure PHP
RUN docker-php-ext-install pdo pdo_mysql zip

# Copy application
COPY . /var/www/html

# Set up entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
```

## 2. Heroku Configuration
```bash
# Create new app
heroku create

# Set stack to container
heroku stack:set container

# Add MySQL database
heroku addons:create jawsdb-maria:kitefin

# Set environment variables
heroku config:set APP_KEY=$(php artisan key:generate --show)
heroku config:set APP_ENV=production

# Deploy
git push heroku main
```

## 3. Database Migration
```bash
# Run migrations on Heroku
heroku run "php artisan migrate --force"

# Seed initial data
heroku run "php artisan db:seed --class=RoleSeeder"
heroku run "php artisan db:seed --class=CompetencySeeder"
```

## Free Tier Considerations
- Schedule daily dyno restart via Heroku Scheduler
- Implement database cleanup job
```php:app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        DB::table('sessions')->where('last_activity', '<', now()->subDay())->delete();
    })->daily();
}
```

[View Full Configuration](/.github/workflows/deploy.yml)