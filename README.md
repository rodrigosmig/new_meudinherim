<h1 align="center">Meu Dinherim</h1>

Table of Contents
=================
<!--ts-->
   * [About](#About)
   * [Features](#Features)
   * [Requirements](#requirements)
   * [Installation](#installation)
   * [Tests](#Tests)
   * [License](#License)
<!--te-->

## About Meu Dinherim <a name="About"></a>

A simple financial system to manage personal expenses.

## Features <a name="Features"></a>

With Meu Dinherim is possible:

- Schedule payments.
- Schedule receivements.
- Create categories.
- Add credit cards.
- Add Bank Accounts.
- Expense reports by category.

## Requirements

Before starting installation you need [Docker](https://docs.docker.com/engine/install/) and [Docker-Compose](https://docs.docker.com/compose/install/) installed.

## Installation <a name="installation"></a>
##### Clone this repository 
```shell
$ git clone https://github.com/rodrigosmig/new_meudinherim
```

##### Access the project folder at the terminal
```shell
$ cd new_meudinherim
```

##### Run the installation script 
```shell
$ sh install.sh
```
##### Go to http://localhost:8082

## Tests <a name="Tests"></a>
To run the feature tests, run: 
```shell
$ docker-compose exec app php artisan test
```
## License <a name="License"></a>

The Meu Dinherim  is free software licensed under the [MIT license](https://opensource.org/licenses/MIT).
