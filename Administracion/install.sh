#!/bin/bash
set -e 
version="3.8.4"
mkdir /var/db
chown -R postgres /var/db
chmod 777 config_db.sh
mv config_db.sh /var/lib/postgresql/
cd  /var/lib/postgresql
su postgres config_db.sh
cp /var/www/orfeo-${version}/config.php.postgres /var/www/orfeo-${version}/config.php
cd -
sed -f config.php.sed -i /var/www/orfeo-${version}/config.php
/etc/init.d/apache2 restart
/etc/init.d/postgresql restart
