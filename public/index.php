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

	die("Not found");

} catch (\Slim\Exception\NotFoundAllowed $e) {

	die("Not allowed");

} catch (Exception $e) {

	die($e);

}
