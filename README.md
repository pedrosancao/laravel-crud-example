# Laravel CRUD Controllers

Laravel CRUD show case with default authentication (session).

There are 4 entities: users, departments, roles and permissions. All relations are defined
as many to many. Each entity has routes to list, create, update and delete. All endpoints
returns JSON response.

To use

## Installation:

- clone this repository;
- install PHP packages: `composer install`
- setup database credentials on .env file
- run migrations: `php artisan migrate --seed`
- start dev server `php artisan serve`
- login with email "admin@sample.com" and password "admin123"
