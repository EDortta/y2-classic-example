#!/bin/bash
if [[ $EUID -eq 0 ]]; then
    echo "This script should not be run as root!" >&2
    exit 1
fi


echo
echo "GRANTING PHP IS INSTALLED AND WITH CORRECT VERSION"

p=`which php` > /dev/null 2>&1
if [ ! -z $p ]; then
    echo "PHP is installed"
    pv=`php -v | grep cli | awk '{print $2}'`
    echo "PHP version: $pv"
    
    # We only accept php versions greater than 8.2
    if [[ $(echo "$pv" | cut -d. -f1) -lt 8 || $(echo "$pv" | cut -d. -f2) -lt 2 ]]; then
        echo "PHP version must be greater than 8.2" >&2
        exit 1
    fi
else
    echo "PHP is not installed"
    exit 1
fi


echo
echo "CHECK IF SUDO IS INSTALLED"

zd=`which sudo` > /dev/null 2>&1
if [ ! -z $zd ]; then
    $zd -k
    echo "We'll need the SUDO password to continue"
fi



echo
echo "GRANTING COMPOSER IS INSTALLED"

c=`which composer` > /dev/null 2>&1
if [ ! -z $c ]; then
    echo "Composer is installed"
else
    echo "Composer is not installed, downloading and installing it..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    $zp php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm composer-setup.php
fi



echo
echo "CREATING DIRECTORIES"

$zd mkdir -p var/logs/nginx
$zd touch var/log/nginx/error.log
$zd rouch var/log/nginx/access.log
$zd chmod -R 777 var/logs

$zd chmod -R 777 var
if [ ! -d web/logs ]; then
    $zd mkdir -p web/logs
fi
$zd chmod -R 777 web/logs

if [ ! -d web/assets ]; then
    $zd mkdir -p web/assets
fi

$zd chmod -R 777 web/assets



( 
    echo
    echo "UPDATING COMPOSER DEPENDENCIES"
    pushd web > /dev/null 2>&1
    composer update
    popd > /dev/null 2>&1
)


echo
echo "BUILDING THE DOCKER IMAGES"

if [ -f "docker-compose-devel.yml" ]; then
    recipe="docker-compose-devel.yml"
else
    recipe="docker-compose.yml"
fi



docker-compose --file $recipe build
docker-compose --file $recipe up -d
