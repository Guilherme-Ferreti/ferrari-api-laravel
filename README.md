# Laravel Ferrari API

This project, built with Laravel Framework and MongoDB, mimics Hcode's Ferrari API originally built with NestJS Framework and MySQL.

## API Documentation

## Requirements

* PHP 8.0.2 or newer.

## Installation

Start by cloning this repository.

```git clone https://github.com/Guilherme-Ferreti/php-slim-boilerplate.git```

Make sure you have installed Composer. If not, please check its official [guide](http://getcomposer.org/doc/00-intro.md#installation).

When ready, install the dependencies by running the following command in your application's root folder:

```composer install```

Copy *.env.example* file and rename it to *.env*. Then, configure your local environment.

Serve you app and that's it!

## Testing

This project utilizes a separate database for testing. By default, the connection is set to be the same as your *.env DB_CONNECTION*. Furthermore, the database used is set to *ferrari-api-test*.

You may change those configuration in your phpunit.xml as you wish.