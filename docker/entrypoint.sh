#!/usr/bin/env bash
set -e

cd /var/www/html

DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_USERNAME="${DB_USERNAME:-storybook}"
DB_PASSWORD="${DB_PASSWORD:-storybook}"
INIT_DB_WITH_SEED="${INIT_DB_WITH_SEED:-true}"
SEED_MARKER_FILE="storage/framework/.seeded"

if [ ! -f .env ]; then
  cp .env.example .env
fi

if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --force
fi

echo "Waiting for database..."
until mysqladmin --skip-ssl ping -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do
  sleep 2
done

php artisan config:clear
php artisan migrate --force

if [ "${INIT_DB_WITH_SEED}" = "true" ] && [ ! -f "${SEED_MARKER_FILE}" ]; then
  php artisan db:seed --force || true
  touch "${SEED_MARKER_FILE}"
fi

if [ ! -L public/storage ]; then
  php artisan storage:link || true
fi

chown -R www-data:www-data storage bootstrap/cache

exec "$@"
