# To Do List

To Do List is a Symfony project for my Openclassrooms training.

## Technologies

-   PHP (Symfony framework)
-   [Twig](https://twig.symftwigony.com/) - Template engine for PHP
-   CSS

## Prerequisite

-   PHP 7.2.5 minimum
-   MySQL 
-   Composer

## Installation

1.  Clone the repository : `git clone https://github.com/YsolineG/projet-oc-todolist.git`
2.  Install composer packages : `composer install`
3.  Configure the SQL DATABASE_URL connection string so that it can communicate with your local SQL database
4.  Create a database for the project : `php bin/console doctrine:schema:create`
5.  Run Doctrine migrations : `php bin/console doctrine:migrations:migrate`
6.  To start the project : `symfony server:start`

## Run the tests

1.  In `.env.test`, configure the SQL DATABASE_URL connection string so that it can communicate with your local SQL database
2.  Create the test database : `php bin/console --env=test doctrine:database:create`
3.  Create the tables/columns in the test database : `php bin/console --env=test doctrine:schema:create`
4.  Fill the database with fixtures : `php bin/console --env=test doctrine:fixtures:load`
5.  Run the tests : `php bin/phpunit`
6.  Run the coverage : `php bin/phpunit --coverage-html coverage`
