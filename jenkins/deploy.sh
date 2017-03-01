#!/bin/bash

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/cache/prod
mkdir -p /var/app/var/logs
mkdir -p /var/app/var/logs/supervisord

#composer config github-oauth.github.com "9b41dc4199ceb4611598caa882e06115931d85f8"
composer install --no-scripts --no-suggest --optimize-autoloader

rm -R /var/app/var/cache/prod/*

rm docker-compose.yml
rm docker-compose-deploy.yml
rm docker-compose-mac.yml
rm docker-compose-nuc.yml
rm run.sh
rm -R jenkins