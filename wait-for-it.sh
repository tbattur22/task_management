#!/usr/bin/env bash

# Wait until Postgres is ready
until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME"; do
  echo "⏳ Waiting for Postgres at $DB_HOST:$DB_PORT..."
  sleep 2
done

echo "✅ Postgres is ready — running migrations..."
php artisan migrate --force
php artisan db:seed --force

# Start Apache
exec apache2-foreground

