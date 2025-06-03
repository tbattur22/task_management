#!/usr/bin/env bash
set -e

# Validate env vars
: "${DB_HOST:?Missing DB_HOST}"
: "${DB_PORT:?Missing DB_PORT}"
: "${DB_USERNAME:?Missing DB_USERNAME}"
: "${DB_PASSWORD:?Missing DB_PASSWORD}"
: "${DB_DATABASE:?Missing DB_DATABASE}"

MAX_RETRIES=30
COUNT=0

until PGPASSWORD=$DB_PASSWORD psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" -c '\q' 2>/dev/null; do
  ((COUNT++))
  echo "⏳ Waiting for Postgres at $DB_HOST:$DB_PORT... ($COUNT/$MAX_RETRIES)"
  if [ "$COUNT" -ge "$MAX_RETRIES" ]; then
    echo "❌ Postgres not available after $MAX_RETRIES attempts."
    exit 1
  fi
  sleep 2
done

echo "✅ Postgres is ready — running migrations..."
php artisan migrate --force

# Start Apache
exec apache2-foreground
