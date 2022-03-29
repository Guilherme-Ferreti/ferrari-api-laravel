# Laravel Ferrari API

This project is built with Laravel Framework and MongoDB. It mimics Hcode's Ferrari API originally built with NestJS Framework and MySQL.

## API Documentation

API Documentation can be found [here](https://ferrari-api-laravel.herokuapp.com/).

There is also an Insomnia file inside docs folder.

## Requirements

* PHP 8.0.2 or newer.

## Installation

Start by cloning this repository.

```git clone https://github.com/Guilherme-Ferreti/ferrari-api-laravel.git```

Make sure you have installed Composer. If not, please check its official [guide](http://getcomposer.org/doc/00-intro.md#installation).

When ready, install the dependencies by running the following command in your application's root folder:

```composer install```

Copy *.env.example* file and rename it to *.env*. Then, configure your local environment. That includes your MongoDB connection.

Serve you app and that's it!

## Testing

This project utilizes a separate database for testing. By default, the connection is set to be the same as your *.env DB_CONNECTION*. Furthermore, the database used is set to *ferrari-api-test*.

You may change those configuration in your phpunit.xml as you wish.
