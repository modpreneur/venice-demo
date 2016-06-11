#!/usr/bin/env bash

#default is -n = do not override files
#with option -f (force) will be overridden all files
CP_OPTION="-n"
while getopts f o
do
    case "$o" in
        f) CP_OPTION="";;
    esac
done

cp -v vendor/modpreneur/venice/runMac.sh run.sh #always override
cp -v vendor/modpreneur/venice/package.json package.json #always override
cp -v $CP_OPTION vendor/modpreneur/venice/entrypoint.sh entrypoint.sh #only once
cp -v $CP_OPTION vendor/modpreneur/venice/docker-compose.yml docker-compose.yml #only once
cp -v $CP_OPTION vendor/modpreneur/venice/Dockerfile Dockerfile #only once
cp -v $CP_OPTION -R vendor/modpreneur/venice/Docker . #only non existing files
cp -v $CP_OPTION -R vendor/modpreneur/venice/js web #only non existing files
cp -v vendor/modpreneur/venice/src/Venice/AppBundle/Resources/config/routing.yml app/config/venice/app_routing.yml #always override
cp -v vendor/modpreneur/venice/src/Venice/AdminBundle/Resources/config/routing.yml app/config/venice/admin_routing.yml #always override
cp -v vendor/modpreneur/venice/src/Venice/FrontBundle/Resources/config/routing.yml app/config/venice/front_routing.yml #always override

echo "Done"