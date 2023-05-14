#!/bin/sh
set -e

# Run migrations
php artisan migrate

# Continue with the default entrypoint command
exec "$@"