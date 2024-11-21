#!/bin/bash

# Init cron tab
service cron start

# Load env vars
source /etc/apache2/envvars

# Run composer
#cd /var/www/html && composer update --no-scripts

# Create db backups dir
cd /var/www/html/storage && mkdir db-backups

# Set 777 to logs files
cd /var/www/html/ && chmod 777 storage -Rf

# Just give some more seconds to allow mysql to be up and running
#sleep 30

# Run migrations and seeds
#cd /var/www/html && php artisan migrate --force --env=local && php artisan db:seed --force --env=local

FILE=/var/www/html/storage/database/db_backup.sql
if [[ -f "$FILE" ]]; then
    echo "$FILE exists. Lets restore the db...."
#    cd /var/www/html && php artisan db:backups --env=local
    echo "Restore done...."
fi

# Start apache and keep it running
exec apache2 -D FOREGROUND

