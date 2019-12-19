<?php
return [
    // slim setup
    "displayErrorDetails" => (getenv("APP_DEBUG")=='true') ? true : false,
    "addContentLengthHeader" => false,
    // app setup
    "app" => [
        "key" => getenv("APP_KEY"),
        "host" => getenv("APP_HOST"),
        "locales" => ["en_GB.UTF-8"],
        "default_locale" => ["en_GB.UTF-8"],
    ],
	// database setup
	"db" => [
		"filepath" => getenv('DB_FILEPATH')
	],
	// feed reader setup
	"feed" => [
		"cache_path" => getenv('FEED_CACHE_PATH'),
	],
    // logger setup
    "log" => [
        "filepath" => getenv('LOG_FILEPATH'),
    ],
    // twig view setup
    "view" => [
        "path" => getenv("VIEW_PATH"),
        "cache" => getenv("VIEW_CACHE_PATH"),
        "debug" => getenv("APP_DEBUG"),
    ],
];
