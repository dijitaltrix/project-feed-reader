<?php
/*
 *	Setup your container dependencies here
 */
// set csrf protection
$container['csrf'] = function ($c) {
	$csrf = new \Slim\Csrf\Guard;
	$csrf->setPersistentTokenMode(true);
	return $csrf;
};
// app logger
$container['log'] = function($c) {
	$log = new \Monolog\Logger('app');
	$log->pushHandler(new \Monolog\Handler\StreamHandler($c->settings["log"]["path"], \Monolog\Logger::DEBUG));
	return $log;
};
// set renderer
$container['view'] = function($c) {
	$view = new \Slim\Views\Twig([
		'app' => path('app/src/App/views'),
		'feeds' => path('app/src/Feeds/views'),
	], [
		'cache' => path('storage/views'),
		'debug' => true,
	]);
	$view->addExtension(new \Twig\Extension\DebugExtension());
	// Instantiate and add Slim specific extension
	$uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
	$view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $uri));
	// Add form_protect function which integrates with Slim/Csrf
	$view->getEnvironment()->addFunction(new \Twig\TwigFunction('form_protect', function() use($c) {
		$csrf = $c->get('csrf');
		$out = sprintf('<input type="hidden" id="_token_name" name="%s" value="%s">', $csrf->getTokenNameKey(), $csrf->getTokenName());
		$out.= sprintf('<input type="hidden" id="_token_value" name="%s" value="%s">', $csrf->getTokenValueKey(), $csrf->getTokenValue());
		return $out;
	}));
	return $view;
};
