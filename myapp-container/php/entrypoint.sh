#!/bin/sh

composer install --working-dir="/opt/myapp"
chown -R www-data:www-data "/opt/myapp"
chmod -R 774 "/opt/myapp"

npm ci --prefix "/opt/myapp"
npm run build --prefix "/opt/myapp"

php-fpm
