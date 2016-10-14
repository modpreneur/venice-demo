#!/bin/bash sh

mkdir -p /var/app/var/logs
mkdir -p /var/app/web/compiled
mkdir -p /var/app/var/xdebug
rm -R /var/app/var/xdebug/*

composer config -g github-oauth.github.com 9b41dc4199ceb4611598caa882e06115931d85f8
composer run-script post-install-cmd --no-interaction

bin/console assetic:dump

ENV=dev supervisord -c vendor/modpreneur/venice/supervisor/supervisord.conf
supervisorctl -c vendor/modpreneur/venice/supervisor/supervisord.conf status

java -jar /opt/selenium-server-standalone.jar -role hub &
phantomjs --webdriver=8080 &


if [ $USER_ID ] ; then
    echo "Chown app folder to user with id $USER_ID"
    useradd --shell /bin/bash -u $USER_ID -o -c "" -m user
    export HOME=/home/user
    chown -R $USER_ID /var/app/
fi

chown -R www-data:www-data /var/app/var/logs
chown -R www-data:www-data /var/app/var/cache

exec apache2-foreground