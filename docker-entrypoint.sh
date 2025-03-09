#!/bin/bash
set -e

echo "Running composer install..."
composer install

echo "Running artisan migrate..."
php artisan migrate --force

echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=8000
