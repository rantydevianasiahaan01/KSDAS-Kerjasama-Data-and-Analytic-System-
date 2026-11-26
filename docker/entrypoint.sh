#!/bin/sh
set -e

echo "Syncing application code from image to volume..."

# Seed awal jika volume kosong
if [ ! -f /app/artisan ]; then
    cp -a /opt/itdel-app/. /app/
else
    # Update kode dari image; storage & bootstrap/cache di volume tidak dihapus
    tar -C /opt/itdel-app \
        --exclude='./storage' \
        --exclude='./bootstrap/cache' \
        --exclude='./.composer' \
        -cf - . | tar -C /app -xf -
fi

cd /app

mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Hanya install jika vendor belum ada
if [ ! -f vendor/autoload.php ]; then
    echo "Vendor missing in volume, installing Composer packages..."
    composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts
    composer dump-autoload --optimize
fi

if [ ! -L public/storage ] && [ ! -e public/storage ]; then
    php artisan storage:link || true
fi

echo "Running optimize:clear && optimize..."
php artisan optimize:clear
php artisan optimize

exec frankenphp run --config /app/docker/Caddyfile --adapter caddyfile
