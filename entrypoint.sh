#!/bin/bash

set -e

run_as_owner() {
		su -s /bin/bash www-data -c "$*"
}

#
# Generate .env only if allowed AND missing
#
if [ -n "$GENERATE_ENV" ] && [ ! -f /var/www/html/.env ]; then
    echo "[entrypoint] Generating default .env"
    cp /var/www/html/.env.test /var/www/html/.env
    chown www-data:www-data /var/www/html/.env
    chmod 664 /var/www/html/.env
fi

#
# Ensure APP_KEY exists
#
if ! grep -q "APP_KEY=" /var/www/html/.env || grep -q "APP_KEY=$" /var/www/html/.env; then
    echo "[entrypoint] Generating APP_KEY"
    run_as_owner "php artisan key:generate --force"
fi

#
# Wait for MySQL
#
echo "[entrypoint] Waiting for MySQL..."
until nc -z "$DB_HOST" "$DB_PORT"; do
    sleep 2
done
echo "[entrypoint] MySQL is ready!"

#
# Optional DB migrations
#
if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
    echo "[entrypoint] Checking migrations..."
    run_as_owner "php artisan migrate:fresh --seed --force"
fi

#
# Clear caches (safe)
#
run_as_owner "php artisan config:clear"
run_as_owner "php artisan route:clear"

echo "[entrypoint] Starting FrankenPHP"
run_as_owner "frankenphp run --config /etc/caddy/Caddyfile"
