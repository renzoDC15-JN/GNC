
## Installation Guide

Prerequisites

    Libre office 
    brew install --cask libreoffice
    

Steps    

- composer install

- php artisan migrate --seed

- php artisan shield:super-admin

- php artisan db:seed --class=ShieldSeeder

