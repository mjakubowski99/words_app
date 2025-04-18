set -e
export XDEBUG_MODE=off;
composer php-cs-fixer;
composer phpstan;
php artisan migrate:fresh --seed;
composer test;
composer open-api;
