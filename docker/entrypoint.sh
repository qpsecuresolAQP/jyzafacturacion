#!/bin/sh
set -e

mkdir -p /var/www/html/tmp/cache/models \
         /var/www/html/tmp/cache/persistent \
         /var/www/html/tmp/cache/views \
         /var/www/html/tmp/sessions \
         /var/www/html/tmp/tests \
         /var/www/html/logs \
         /var/www/html/webroot/uploads \
         /var/www/html/webroot/xml \
         /var/www/html/webroot/cdr \
         /var/www/html/webroot/pdf \
         /var/www/html/webroot/certificados \
         /var/www/html/webroot/img

chown -R www-data:www-data /var/www/html/tmp /var/www/html/logs \
    /var/www/html/webroot/uploads /var/www/html/webroot/xml \
    /var/www/html/webroot/cdr /var/www/html/webroot/pdf \
    /var/www/html/webroot/certificados /var/www/html/webroot/img
chmod -R 775 /var/www/html/tmp /var/www/html/logs \
    /var/www/html/webroot/uploads /var/www/html/webroot/xml \
    /var/www/html/webroot/cdr /var/www/html/webroot/pdf \
    /var/www/html/webroot/certificados /var/www/html/webroot/img

if [ ! -f /var/www/html/config/app_local.php ] && [ -f /var/www/html/config/app_local.example.php ]; then
    cp /var/www/html/config/app_local.example.php /var/www/html/config/app_local.php
fi

rm -f /var/www/html/tmp/cache/models/* \
      /var/www/html/tmp/cache/persistent/* \
      /var/www/html/tmp/cache/views/* 2>/dev/null || true

echo "[entrypoint] App ready. Starting Apache..."

exec "$@"