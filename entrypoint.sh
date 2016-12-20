#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs
mkdir -p /var/app/var/logs/supervisord

composer run-script post-install-cmd --no-interaction

composer config github-oauth.github.com "9b41dc4199ceb4611598caa882e06115931d85f8"

bin/console assetic:dump --env=prod --no-debug

echo "export ENV=prod" >> /etc/bash.bashrc

ENV=prod supervisord -c vendor/modpreneur/venice/supervisor/supervisord.conf
supervisorctl -c vendor/modpreneur/venice/supervisor/supervisord.conf reload

chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/var/logs

exec apache2-foreground