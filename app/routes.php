<?php
/**
 * This is where all your app routes go
 * It's also possible to include routes from other files
 */
$app->get('/', function($request, $response, $args) {

	return $response->withRedirect('/feeds', 302);

});
