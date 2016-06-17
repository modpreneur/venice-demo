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

echo "Done"