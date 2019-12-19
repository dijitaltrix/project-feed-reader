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
// set database
$container['db'] = function ($c) {
    $db_filename = getenv('APP_DB_FILENAME');

    try {
        $db = new \PDO(sprintf("%s:%s", "sqlite", path("database/$db_filename")));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    } catch (PDOException $e) {

        // for this demo just change and catch this in index.php
        throw new Exception($e->getMessage());
    }
};
// input filter
$container['filter'] = function ($c) {
    return new \App\Filter;
};
// app logger
$container['log'] = function ($c) {
    $log = new \Monolog\Logger('app');
    $log->pushHandler(new \Monolog\Handler\StreamHandler($c->settings["log"]["path"], \Monolog\Logger::DEBUG));
    return $log;
};
// feed reader
$container['feed_reader'] = function ($c) {
    $reader = new SimplePie();
    $reader->set_cache_location(path('storage/cache/feeds'));
    return $reader;
};
// flash messages
$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages();
};
// session handler
$container['session'] = function ($c) {
    return new \RKA\Session();
};
// view renderer
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig([
        'app' => path('app/src/App/views'),
        'feeds' => path('app/src/Feeds/views'),
    ], [
        'cache' => path('storage/cache/views'),
        'debug' => getenv('APP_DEBUG'),
    ]);
    // Instantiate and add Slim specific extension
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $uri));
    // Add form_protect function which integrates with Slim/Csrf
    $view->getEnvironment()->addFunction(new \Twig\TwigFunction('form_protect', function () use ($c) {
        $csrf = $c->get('csrf');
        $out = sprintf('<input type="hidden" id="_token_name" name="%s" value="%s">', $csrf->getTokenNameKey(), $csrf->getTokenName());
        $out.= sprintf('<input type="hidden" id="_token_value" name="%s" value="%s">', $csrf->getTokenValueKey(), $csrf->getTokenValue());
        return $out;
    }));
    return $view;
};
