SHELL := /bin/bash

down:
	docker-compose down -v

up:
	docker-compose up -d --build

install:
	docker exec -it app composer install

key-generate:
	docker exec -it app php artisan key:generate

migrate:
	docker exec -it app php artisan migrate

seed:
	docker exec -it app php artisan db:seed

optimize:
	docker exec -it app php artisan optimize

serve:
	make down
	make up
	make install
	make key-generate
	make migrate
	make seed
	make optimize
