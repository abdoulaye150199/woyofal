#!/bin/bash

# Start PHP-FPM
/usr/local/sbin/php-fpm -D

# Replace environment variables in nginx config
envsubst '$PORT' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf.tmp
mv /etc/nginx/conf.d/default.conf.tmp /etc/nginx/conf.d/default.conf

# Start Nginx
nginx -g "daemon off;"
