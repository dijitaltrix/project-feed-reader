# Project Feed Reader

### Table of Contents 
* [Requirements](#requirements)
* [Installation](#installation)
* [Run](#run)

## Requirements

* PHP 5.6 or higher with the following extensions:
	* ext-json
	* ext-libxml
	* ext-pcre
	* ext-pdo-sqlite
	* ext-simplexml
	* ext-xml
	* ext-xmlreader

## Install

####Clone this repository

```sh
git clone https://github.com/dijitaltrix/project-feed-reader.git
```

####Install the dependencies with [Composer](http://getcomposer.org):

`cd` into the new folder and run composer update

```sh
composer update
```

####Create the database

```sh
vendor/bin/phinx migrate
 # Optionally seed the database with the example feeds
vendor/bin/phinx seed
```

## Run

Use the built in PHP server to run the app

```sh
php -S localhost:8000 -t public
```

Then open the app in your favourite browser
