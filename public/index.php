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

} catch (\Slim\Exception\NotFoundException $e) {

	// log to events, good to track these somewhere
	$container->log->info($e);
	// show a nice helpful error page with text describing what to do next
	die("Page not found");

} catch (\Slim\Exception\NotFoundAllowed $e) {

	// log error, might be malicious
	$container->log->info($e);
	die("Not allowed");

} catch (PDOException $e) {

	// log error 
	$container->log->error($e);
	// handle database errors
	die("Sorry something went wrong with the database, did you set it up correctly?");

} catch (Exception $e) {

	// log error
	$container->log->error($e);
	// handle any other errors, show helpful page assuring user it's not their fault
	die($e);

}
