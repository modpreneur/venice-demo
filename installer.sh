#!/usr/bin/env bash

cp vendor/modpreneur/venice/runMac.sh run.sh
cp vendor/modpreneur/venice/package.json package.json
cp vendor/modpreneur/venice/entrypoint.sh entrypoint.sh
cp vendor/modpreneur/venice/docker-compose.yml docker-compose.yml
cp vendor/modpreneur/venice/Dockerfile Dockerfile
cp -R vendor/modpreneur/venice/Docker .
cp -R vendor/modpreneur/venice/js web
cp vendor/modpreneur/venice/src/Venice/AppBundle/DoctrineMigrations/* src/AppBundle/DoctrineMigrations
cp vendor/modpreneur/venice/src/Venice/AppBundle/Resources/config/routing.yml app/config/venice/app_routing.yml
cp vendor/modpreneur/venice/src/Venice/AdminBundle/Resources/config/routing.yml app/config/venice/admin_routing.yml
cp vendor/modpreneur/venice/src/Venice/FrontBundle/Resources/config/routing.yml app/config/venice/front_routing.yml

echo "Done"