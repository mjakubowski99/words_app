#!/bin/bash

cd docker/ &&
docker-compose -f docker-compose.prod.yml build &&
docker-compose -f docker-compose.prod.yml down &&
docker-compose -f docker-compose.prod.yml up -d &&
docker exec words_prod_php php artisan optimize:clear &&
docker exec words_prod_php php artisan optimize &&
docker exec words_prod_php php artisan migrate --force



