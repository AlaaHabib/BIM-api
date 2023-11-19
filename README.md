## BIM API

## System Requirements
php 8.1 or above mysql

## Installation steps
Run the following commands

- composer install / update
- copy .env.example as .env file and set database credentials
- php artisan migrate --seed
- php artisan key:generate
- php artisan l5-swagger:generate

## Swagger Doc.
View http://localhost:8000/api/documentation
