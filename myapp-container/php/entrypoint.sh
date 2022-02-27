#!/bin/sh

if [ "$ENVIRONMENT" != "development" ] ; then
    composer install --working-dir="/opt/myapp"
    npm ci --prefix "/opt/myapp"
    npm run build --prefix "/opt/myapp"
fi

chown -R www-data:www-data "/opt/myapp"
chmod -R 774 "/opt/myapp"

php-fpm
