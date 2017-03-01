#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs
mkdir -p /var/app/var/logs/supervisord

#bin/console redis:flushall --no-interaction

composer run-script post-install-cmd --no-interaction

ENV=prod supervisord -c vendor/modpreneur/venice/supervisor/supervisord.conf
supervisorctl -c vendor/modpreneur/venice/supervisor/supervisord.conf reload


chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/var/logs
chown -R www-data:www-data /var/app/var/cache
chown -R www-data:www-data /var/app/var/logs

exec php-fpm