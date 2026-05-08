#!/bin/sh
set -e

if [ -f .env ] && ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force --ansi
fi

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
    php artisan migrate --force --no-interaction
fi

exec "$@"
