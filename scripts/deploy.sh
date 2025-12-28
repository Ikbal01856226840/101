#!/usr/bin/env bash

# CD into the code directory
cd /var/www/html/101

# enter a maintainance mode
php artisan down

# get the latest code
git pull

# install the app's dependencies
composer install --no-dev

# migrate the database
#php artisan migrate --force

# cache the app's config for speed boosting
php artisan config:cache

# run other necessary commands for your spcecif application
# ex
# php artisan queue:work

# compile javascripts assets
#npm run build

# exit the maintainance mode
php artisan up