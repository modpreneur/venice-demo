#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs
mkdir -p /var/app/var/logs/supervisord

composer config -g github-oauth.github.com 9b41dc4199ceb4611598caa882e06115931d85f8


if [ -f "vendor/autoload.php" ]; then
    echo "Vendors are already installed";
else
    composer install --dev --no-scripts --no-suggest --optimize-autoloader --apcu-autoloader
fi

#Ríša nezařídil
#if [ -d "web/js/node_modules" ]; then
#    echo "Node modules are already installed";
#else
#    npm run install_only
#fi

#Ríša nezařídil
#if [ -f "web/js/dist/necktie.bundle.js" ]; then
#    echo "Dev fallback is already created";
#else
#    npm run dev
#fi

composer run-script post-install-cmd --no-interaction


if [ $USER_ID ] ; then
    echo "Chown app folder to user with id $USER_ID"
    chown -R $USER_ID /var/app/
fi

chown -R www-data:www-data /var/app/var/logs
chmod -R 0777 /var/app/var/logs

#supervisor - load config from venice framework
supervisord -c vendor/modpreneur/venice/supervisor/supervisord.conf
supervisorctl -c vendor/modpreneur/venice/supervisor/supervisord.conf status

chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/var/logs
chown www-data:www-data /var/app/var/logs
chown www-data:www-data /var/app/var/cache

exec php-fpm