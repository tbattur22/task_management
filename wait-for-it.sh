#!/usr/bin/env bash

# Wait until Postgres is ready
until PGPASSWORD=$DB_PASSWORD psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" -c '\q' 2>/dev/null; do
  echo "⏳ Waiting for Postgres at $DB_HOST:$DB_PORT..."
  sleep 2
done

echo "✅ Postgres is ready — running migrations..."
php artisan migrate --force

# Start Apache
exec apache2-foreground

