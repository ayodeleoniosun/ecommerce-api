#!/bin/bash

set -e

GREEN=$(tput setaf 2)

echo "${GREEN}🧱 Starting Application setup..."

echo "${GREEN}🔄 Bringing up containers..."
docker-compose down -v
docker-compose up -d --build

echo "${GREEN}📦 Installing Composer dependencies..."
docker exec -it app composer install

echo "${GREEN}🔑 Generating app key..."
docker exec -it app php artisan key:generate

echo "${GREEN}🚀 Migrating database..."
docker exec -it app php artisan migrate

echo "${GREEN}🚀 Running seeders..."
docker exec -it app php artisan db:seed

echo "${GREEN}🚀 Starting Horizon..."
docker exec -it app php artisan horizon

echo "${GREEN}✅ Application setup complete!"
