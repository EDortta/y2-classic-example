#!/bin/bash
if [ -f "docker-compose-devel.yml" ]; then
    recipe="docker-compose-devel.yml"
else
    recipe="docker-compose.yml"
fi

docker-compose --file $recipe down