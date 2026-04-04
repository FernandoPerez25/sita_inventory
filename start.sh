#!/bin/bash
php artisan migrate:fresh --force --seed
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}