#!/bin/bash

set -e

PINK=$(tput setaf 5)

echo "${PINK}🧱 Starting Application setup..."

echo "${PINK}🔄 Bringing up containers..."
docker-compose down -v
docker-compose up -d --build

echo "${PINK}📦 Installing Composer dependencies..."
docker exec -it app composer install

echo "${PINK}🔑 Generating app key..."
docker exec -it app php artisan key:generate

echo "${PINK}🚀 Migrating database..."
docker exec -it app php artisan migrate

echo "${PINK}🚀 Running seeders..."
docker exec -it app php artisan db:seed

echo "${PINK}✅ Application setup complete!"
