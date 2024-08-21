#!/bin/bash
if [ -f "docker-compose-devel.yml" ]; then
    recipe="docker-compose-devel.yml"
else
    recipe="docker-compose.yml"
fi

dc=`which docker-compose` > /dev/null 2>&1
if [ -z $dc ]; then
    dc="docker compose -f $recipe"
else 
    dc="docker-compose --file $recipe"
fi

cmd="$dc down $@"
echo "CMD = $cmd"
$cmd