<?php

try {

    # setup the environment
    include '../init.php';

    # bootstrap the app
    include path("app/boot.php");

    # include the routes
    require path("app/routes.php");

    # run the application
    $app->run();

    #
    #	The exception handling below should really be
    #	put into an exception handler class and linked
    #	from slims container
    #
} catch (\Slim\Exception\NotFoundException $e) {

    // log to events, good to track these somewhere
    $container->log->info($e);

    // show helpful page assuring user it's not their fault
    $view = $container->get('view');
    $response = $container->get('response');

    header("HTTP/1.1 404 Page Not Found");
    echo $view->fetch('@app/errors/4xx.twig', [
        // wouldn't really show exception messages here
        'code' => 404,
        'message' => $e->getMessage(),
    ]);
} catch (PDOException $e) {

    // log error
    $container->log->error($e);

    // show helpful page assuring user it's not their fault
    $view = $container->get('view');
    $response = $container->get('response');

    header("HTTP/1.1 500 Server Error");
    echo $view->fetch('@app/errors/5xx.twig', [
        // wouldn't really show exception messages here
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
    ]);
} catch (Exception $e) {

    // log error
    $container->log->error($e);

    // show helpful page assuring user it's not their fault
    $view = $container->get('view');
    $response = $container->get('response');

    header("HTTP/1.1 500 Server Error");
    echo $view->fetch('@app/errors/5xx.twig', [
        // wouldn't really show exception messages here
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
    ]);
}
