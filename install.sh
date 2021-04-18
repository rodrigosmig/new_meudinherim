#!/bin/bash

docker-compose up -d

echo "Installing dependecies"
docker-compose exec app composer install

echo "Creating .env file"
cp .env.example .env

echo "running the table migration"
docker-compose exec app php artisan migrate

echo "generate key"
docker-compose exec app php artisan key:generate

echo "Configure cache"
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

echo "Go to http://localhost:8082"
