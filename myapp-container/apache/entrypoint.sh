#!/bin/sh

chown -R www-data:www-data "/opt/myapp"
chmod -R 774 "/opt/myapp"

httpd-foreground
