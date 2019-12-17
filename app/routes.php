<?php
/**
 * This is where all your app routes go
 * It's also possible to include routes from other files
 */
include(path('app/src/Feeds/routes.php'));

// help the user by adding a redirect from root to feeds as it's the only thing we've got
$app->get('/', function ($request, $response, $args) {
    return $response->withRedirect('/feeds', 302);
});
