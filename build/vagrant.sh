#!/bin/bash

COMPOSER="/usr/local/bin/composer"
DEBIAN_FRONTEND="noninteractive"
MYSQL_USER="root"
MYSQL_PASSWORD="password"
MYSQL_DATABASE="phpunit_tests"

sed -i "/mirror:\\/\\//d" /etc/apt/sources.list
sed -i "1ideb mirror://mirrors.ubuntu.com/mirrors.txt precise main restricted universe multiverse" /etc/apt/sources.list
sed -i "1ideb mirror://mirrors.ubuntu.com/mirrors.txt precise-updates main restricted universe multiverse" /etc/apt/sources.list
sed -i "1ideb mirror://mirrors.ubuntu.com/mirrors.txt precise-backports main restricted universe multiverse" /etc/apt/sources.list
sed -i "1ideb mirror://mirrors.ubuntu.com/mirrors.txt precise-security main restricted universe multiverse" /etc/apt/sources.list

apt-get update

debconf-set-selections <<< "mysql-server mysql-server/root_password password $MYSQL_PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $MYSQL_PASSWORD"

apt-get install git php5-cli php5-xdebug php5-sqlite php5-mysql mysql-server-5.5 -y --no-install-recommends

mysql -u root -p"$MYSQL_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $MYSQL_DATABASE;"

if [ ! -f "$COMPOSER" ]; then
    php -r "readfile('https://getcomposer.org/installer');" | sudo php -d apc.enable_cli=0 -- --install-dir=$(dirname "$COMPOSER") --filename=$(basename "$COMPOSER")
else
    sudo "$COMPOSER" self-update
fi

cd /vagrant

cp phpunit.xml.dist phpunit.xml
sed -i 's/<!--//g' phpunit.xml
sed -i 's/-->//g' phpunit.xml

if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
    ${COMPOSER} install --no-interaction --prefer-source --dev
fi
