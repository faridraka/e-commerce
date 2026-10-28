#!/bin/sh
set -e
cd /var/www/html

echo ">> $(php -v | head -n1)"

# 1. Buat project Laravel jika folder masih kosong
if [ ! -f composer.json ]; then
  echo ">> composer.json tidak ditemukan, membuat project Laravel terbaru..."
  composer create-project laravel/laravel /tmp/laravel --prefer-dist --no-interaction
  cp -a /tmp/laravel/. .
  rm -rf /tmp/laravel
fi

# 2. File .env
[ -f .env ] || cp .env.example .env

set_env() {
  if grep -qE "^#? *$1=" .env; then
    sed -i -E "s|^#? *$1=.*|$1=$2|" .env
  else
    echo "$1=$2" >> .env
  fi
}
set_env DB_CONNECTION mysql
set_env DB_HOST db
set_env DB_PORT 3306
set_env DB_DATABASE "$DB_DATABASE"
set_env DB_USERNAME "$DB_USERNAME"
set_env DB_PASSWORD "$DB_PASSWORD"

# 3. Dependency PHP + audit keamanan
composer install --no-interaction --prefer-dist
echo ">> laravel/framework: $(composer show laravel/framework 2>/dev/null | grep -m1 '^versions' || echo 'tidak ditemukan')"
composer audit || echo "!! PERINGATAN: composer audit menemukan advisory, jalankan 'composer update' lalu cek lagi."

# 4. APP_KEY
grep -qE '^APP_KEY=.+' .env || php artisan key:generate --force

# 5. Tunggu database siap
echo ">> Menunggu database..."
until php -r 'try { new PDO("mysql:host=db;dbname=".getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); } catch (Throwable $e) { exit(1); }' 2>/dev/null; do
  sleep 2
done

# 6. Migrasi & storage link
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force
fi
php artisan storage:link 2>/dev/null || true

exec "$@"
