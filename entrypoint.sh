#!/bin/bash sh

composer run-script post-install-cmd --no-interaction

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs
chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/var/logs

composer config github-oauth.github.com "9b41dc4199ceb4611598caa882e06115931d85f8"

exec apache2-foreground