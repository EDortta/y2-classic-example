#!/bin/bash
if [[ $EUID -eq 0 ]]; then
    echo "This script should not be run as root!" >&2
    exit 1
fi

zd=`which sudo` > /dev/null 2>&1
if [ ! -d var/logs ]; then
    mkdir -p var/logs
fi
$zd chmod -R 777 var

if [ ! -d web/logs ]; then
    mkdir -p web/logs
fi
$zd chmod -R 777 web/logs

if [ ! -d web/assets ]; then
    mkdir -p web/assets
fi

$zd chmod -R 777 web/assets

if [ -f "docker-compose-devel.yml" ]; then
    recipe="docker-compose-devel.yml"
else
    recipe="docker-compose.yml"
fi

docker-compose --file $recipe build
docker-compose --file $recipe up -d
