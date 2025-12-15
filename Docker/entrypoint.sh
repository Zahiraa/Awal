#!/bin/sh
set -e

# Ensure .env.local exists
if [ ! -f .env.local ]; then
    cp .env .env.local
fi

# Install Composer dependencies if vendor is missing
if [ ! -d vendor ]; then
    composer install --no-interaction --prefer-dist
fi

exec "$@"
