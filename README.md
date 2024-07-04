
## Installation Guide

Prerequisites

    Libre office 
    brew install --cask libreoffice
    Path of soffice.exe of libreoffice
    LIBREOFFICE_PATH=
    

Steps    

- composer install

- php artisan migrate --seed

- php artisan shield:super-admin

- php artisan db:seed --class=ShieldSeeder

