## About Checkout SVC

## Contents

-   [Basic Requirements](#basic-requirements)
-   [Installing Tools](#installing-tools)

## Basic Requirements

1. Apache server
2. PHP > 8
3. Mysql or MariaDB

## Installing Tools

1. Create a user and database in Mysql/MariaDB

```mysql
    CREATE USER 'your_user'@'localhost' IDENTIFIED BY 'your_password';
    CREATE DATABASE your_database CHARACTER SET utf8mb4;
    GRANT ALL PRIVILEGES ON your_database . * TO 'your_user'@'localhost';
```

2. Copy .env.example to .env and modify database connection section:

```mysql
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_user
    DB_PASSWORD=your_password
```

3. Run install composer to add Laravel dependencies

```sh
    php install composer
```

4. Ejecute Laravel migrations to add the tables on your database

```sh
    php artisan migrate
```

5. Run the app

```sh
    php artisan serve
```

6. Check open api specs folder in order to see the available routes and the parameters needed.
   Specification could be find under api/ folder.
    ```
    POST       api/v1/auth/login
    POST       api/v1/auth/register
    POST       api/v1/cart/checkout
    GET|HEAD   api/v1/cart/products
    POST       api/v1/cart/products
    DELETE     api/v1/cart/products/{externalId}
    GET|HEAD   api/v1/products/{integration}/{externalId}
    ```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
