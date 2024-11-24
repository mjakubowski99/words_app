## VocaSmart / VocaSmart api

## Overview
Flashcards API 

## Installation

1. Clone repository
2. Clone `.env.example` as `.env` from `/entry`.
   ```
    cp .env.example .env
   ```
4. Go to `/entry/docker`
5. Clone `.env.example` as `.env`  from `/entry/docker`.
6. Setup `.env` variables.
7. Run `docker-compose up -d` from `/entry/docker` folder.
9. Run `docker exec -it words_php composer install`
10. Run `docker exec -it words_php php artisan key:generate`
11. Run `docker exec -it words_php php artisan migrate:fresh`

## Run tests
1. Run `docker exec words_php composer test` to run integration and unit tests.
2. Run `docker exec words_php composer php-cs-fixer` to run cs fixer.
3. Run `docker exec words_php composer phpstan;` to run phpstan.
4. Run `docker exec words_php composer open-api` to generate fresh api docs.
This will generate two files in folder `entry/public/openapi` called `api-docs.json` and `api-docs.yaml`.
Visit: localhost:8001/api/documentation


