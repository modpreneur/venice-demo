#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs
mkdir -p /var/app/web/compiled

RUN composer config -g github-oauth.github.com 9b41dc4199ceb4611598caa882e06115931d85f8

composer run-script post-install-cmd --no-interaction

bin/console assetic:dump 

chmod -R 0777 /var/app/var/logs
chmod -R 0777 /var/app/var/cache
chmod -R 0777 var/app/web/compiled

service postfix start

service cron start

chmod +x /var/app/docker/supervisor-manager.sh
./docker/supervisor-manager.sh start

java -jar /opt/selenium-server-standalone.jar -role hub &
phantomjs --webdriver=8080 &

exec apache2-foreground