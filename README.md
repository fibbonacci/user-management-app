# Application for User Management

This application allows to perform CRUD operations on users

## Prerequisites

You need docker and docker-compose to be installed on your machine

## How to run the Application

1. Download the project using `git clone git@github.com:fibbonacci/user-management-app.git`
2. Enter project directory by `cd user-management-app`
3. Then get `.env` file just by copying it from example  `cp .env.example .env`
4. Run the app using `docker-compose up -d`
5. Enter the application container by `docker-compose exec app bash`
6. Run composer install `composer install`
7. Run migrations `php vendor/bin/doctrine-migrations migrate --no-interaction`

## Usage

There are several API endpoint you can use to manipulate with users:

* [GET] http://localhost/users - Retrieves users list.
* [GET] http://localhost/users/{userId} - Retrieves a user by id.
* [POST] http://localhost/users - Creates a new user based on passed parameters in the json body.
```json
{
  "name": "billgates",
  "email": "billgates@test.com",
  "notes": "Some notes about Bill"
}
```
* [PUT] http://localhost/users/{userId} - Updates a user by id.
* [DELETE] http://localhost/users/{userId} - Deletes a user by id.

## Tests

You can run several quality check tools using composer scripts inside your app container:

* `composer phpcs` - Runs PHP Code Sniffer
* `composer phpstan` - Runs PHPStan static analyse
* `composer test` - Runs PHPUnit tests
* `composer test:all` - Runs all of the above checks