#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

mkdir -p storage/app/snapshots storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -d vendor ]; then
    composer install --no-interaction --prefer-dist
fi

if [ -f artisan ] && ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    php artisan key:generate --force
fi

if [ -f package-lock.json ] && [ ! -x node_modules/.bin/vite ]; then
    npm ci
elif [ -f package.json ] && [ ! -x node_modules/.bin/vite ]; then
    npm install
fi

if [ -f package.json ] && [ ! -f public/build/manifest.json ]; then
    npm run build
fi

if [ "${APP_ENV:-local}" = "local" ] && [ "${LOCAL_ADMIN_ENSURE_ON_BOOT:-true}" = "true" ]; then
    php artisan mural:ensure-local-admin --no-interaction || true
fi

exec "$@"
