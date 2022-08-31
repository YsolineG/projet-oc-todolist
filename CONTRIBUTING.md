# Contribution guide

## Install the project

This project uses PHP with the Symfony framework and MySQL.

Here are the instructions to install the project on your machine and get it working :

1.  Clone the repository : `git clone https://github.com/YsolineG/projet-oc-todolist.git`
2.  Install composer packages : `composer install`
3.  Configure the SQL DATABASE_URL connection string so that it can communicate with your local SQL database
4.  Create a database for the project : `php bin/console doctrine:schema:create`
5.  Run doctrine migrations : `php bin/console doctrine:migrations:migrate`
6.  To start the project : `symfony server:start`

## Code guidelines

To ensure the quality and maintainability of the project, some coding style must be followed :

-   The code should stick to the [PSR](https://www.php-fig.org/psr/)
    -   Namespaces and classes must be follow an "autoloading" PSR
    -   Class names must be written in UpperCamelCase
    -   The names of methods and properties must be written in camelCase
    

-   The code should stick to the [Symfony coding standards](https://symfony.com/doc/4.4/contributing/code/standards.html)

## Testing

Every time tou add a feature or fix a bug, your code should be tested with PHPUnit. Here are our standards for testing :

-   The controllers should be tested with functional tests.
-   The code coverage must always be greater than 70%.

Configuration to run the tests :

1.  In `.env.test`, configure the SQL DATABASE_URL connection string so that it can communicate with your local SQL database
2.  Create the test database : `php bin/console --env=test doctrine:database:create`
3.  Create the tables/columns in the test database : `php bin/console --env=test doctrine:schema:create`
4.  Fill the database with fixtures : `php bin/console --env=test doctrine:fixtures:load`
5.  Run the tests : `php bin/phpunit`
6.  Run the coverage : `php bin/phpunit --coverage-html coverage`
    
## Git guidelines

To start working on a new feature or a bug, you need to follow some steps :

1.  You must create a new issue on the repo describing what you are going to work on
2.  You must create a branch respecting the kebab-case : `git checkout -b feat/new-feature`
3.  For [commit messages](https://www.conventionalcommits.org/en/v1.0.0/), you must follow naming standards
4.  You can push your commits `git push` and create a pull request
