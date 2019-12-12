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
	// logger setup
	"log" => [
		"path" => path("storage/logs/app.log"),
		"email" => null,
	],
	// twig view setup
	"view" => [
		"path" => path("app/src/views"),
		"cache" => path("storage/views"),
	],
];
