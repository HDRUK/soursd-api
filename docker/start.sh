#!/bin/bash
set -e

if [ -e /var/www/.env ]; then
    source /var/www/.env
fi

base_command="php artisan octane:frankenphp --max-requests=250 --host=0.0.0.0 --port=8100"

run_queue_worker() {
    while true; do
        echo "[queue] starting worker..."
        php artisan queue:work \
            --sleep=1 \
            --tries=3 \
            --timeout=120 \
            --backoff=5

        echo "[queue] worker stopped or crashed. restarting in 5 seconds..."
        sleep 5
    done
}

if [ "$APP_ENV" = 'local' ]; then
    echo 'running in dev mode - with watch'
    # base_command="$base_command --watch"

    if [ "$REBUILD_DB" = "1" ]; then
        # Completely clear down the data in local/dev envs
        php artisan migrate:fresh
        php artisan db:seed --class=BaseDemoSeeder
    else
        php artisan migrate
    fi
else
    # Only forward-facing migrations anywhere else
    php artisan migrate
    # call the email template seeder to updateOrCreate without truncating first
    DISABLE_TRUNCATE=true php artisan db:seed --class=EmailTemplatesSeeder
    php artisan validation:generate-logs

    echo "running in prod mode"
fi

# Run pending deployment steps (run-once, forward-only data fixes/seeding).
# Must run after migrations so the deployment_steps table and schema exist.
echo "Running deployment steps..."
php artisan deploy:run

# Start queue worker in background WITH restart loop
run_queue_worker &

# Handle shutdown properly
trap "echo 'Stopping...'; kill 0; exit 0" SIGTERM SIGINT

# Start Octane (main process)
exec $base_command