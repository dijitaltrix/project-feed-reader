<?php
/*
 *	Setup your container dependencies here
 */
// app logger
$container['log'] = function($c) {
	$log = new \Monolog\Logger('app');
	$log->pushHandler(new \Monolog\Handler\StreamHandler($c->settings["log"]["path"], \Monolog\Logger::DEBUG));
	return $log;
};
// set renderer
$container['view'] = function($c) {
	$view = new \Slim\Views\Twig([
		'feeds' => path('app/src/Feeds/views'),
	], [
		'cache' => path('storage/views'),
		'debug' => true,
	]);
	$view->addExtension(new \Twig\Extension\DebugExtension());
	// Instantiate and add Slim specific extension
	$uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
	$view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $uri));
	return $view;
};
/* Must boot eloquent on start, not inside container on demand */
$cfg = $container->settings["database"]["default"];
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection([
	'driver' => 'sqlite',
	'database' => path('app/database.sqlite3'),
	'charset' => 'utf8',
	'collation' => 'utf8_unicode_ci',
	'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container['eloquent'] = function ($c) use ($capsule) {
	return $capsule;
};
