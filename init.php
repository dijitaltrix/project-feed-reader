<?php
/*
 *	Setup the application environment and dependencies
 */

define('BASE_PATH', dirname(__FILE__));

// use composer autoloader
require __DIR__."/vendor/autoload.php";

// use dotenv for environment config
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// configure php
ini_set("display_errors", getenv("APP_DEBUG")=='true' ?: false);
ini_set("date.timezone", getenv("APP_TIMEZONE"));
