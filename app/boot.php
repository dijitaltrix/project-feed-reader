<?php
// create app and load settings
$app = new \Slim\App([
    "settings" => include(path("app/settings.php"))
]);
// use Slims default container
$container = $app->getContainer();
// load only .php files in the boot folder
foreach (scandir(path('app/boot')) as $file) {
    $f = path("app/boot/$file");
    if (is_file($f) && (substr($f, -4) == '.php')) {
        require $f;
    }
}
