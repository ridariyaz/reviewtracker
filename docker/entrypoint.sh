#!/bin/bash
set -euo pipefail

PORT="${PORT:-8080}"

# Make Apache listen on the platform-provided port
sed -ri "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -ri "s/:80>/:${PORT}>/g" /etc/apache2/sites-available/*.conf

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

# Ensure APP_KEY exists when platforms inject env vars without a key
if [ -z "${APP_KEY:-}" ]; then
  php artisan key:generate --force --show >/dev/null 2>&1 || true
fi

php artisan storage:link --force >/dev/null 2>&1 || true
php artisan config:clear >/dev/null 2>&1 || true
php artisan migrate --force

exec "$@"
