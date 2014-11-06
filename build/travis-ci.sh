#!/bin/bash

COMPOSER="/usr/local/bin/composer"
COMPOSER_PATH=$(dirname ${COMPOSER})
MYSQL_USER="root"
MYSQL_DATABASE="phpunit_tests"

if [ ! -x "${COMPOSER}" ]; then
    echo "Installing Composer"
    curl -sS https://getcomposer.org/installer | sudo php -d apc.enable_cli=0 -- --install-dir=${COMPOSER_PATH} --filename=$(basename ${COMPOSER})
else
    echo "Updating Composer"
    sudo ${COMPOSER} self-update
fi

${COMPOSER} install --no-interaction --prefer-source --dev

mysql -u ${MYSQL_USER} -e "CREATE DATABASE IF NOT EXISTS $MYSQL_DATABASE;"

sed -i 's/<!--//g' build/travis-ci.xml
sed -i 's/-->//g' build/travis-ci.xml
