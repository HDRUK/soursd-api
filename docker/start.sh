#!/bin/bash

if [ -e /var/www/.env ]; then
    source /var/www/.env
fi

echo "===> Starting Laravel setup..."

if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "dev" ]; then
    echo "Running in development mode with watch enabled"

    if [ "$REBUILD_DB" = 1 ]; then
        echo "Rebuilding DB..."
        php artisan migrate:fresh
        php artisan db:seed --class=BaseDemoSeeder
    else
        php artisan migrate
    fi
else
    echo "Running in production mode"

    php artisan migrate
    DISABLE_TRUNCATE=true php artisan db:seed --class=EmailTemplatesSeeder
    php artisan validation:generate-logs
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

php artisan horizon &

# php artisan octane:frankenphp \
#   --host=0.0.0.0 \
#   --port=8100 \
#   --max-requests=250 \
#   --workers=auto

php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8100 --workers=auto --max-requests=50