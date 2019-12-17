<?php
/*
 *	Define your middleware below
 *	NOTE: Middleare is executed inside out, therefore this file will be executed in bottom up order
 */
$app->add(new \RKA\SessionMiddleware(['name' => 'session']));
