<?php
/*
 *	Feeds routes
 */
$app->get('/feeds', '\Feeds\FeedsController:index')->setName('feeds');
$app->get('/feeds/create', '\Feeds\FeedsController:create')->setName('feeds.create');
$app->post('/feeds', '\Feeds\FeedsController:insert')->setName('feeds.insert');
$app->get('/feeds/{id:[0-9]+}/edit', '\Feeds\FeedsController:edit')->setName('feeds.edit');
$app->get('/feeds/{id:[0-9]+}', '\Feeds\FeedsController:view')->setName('feeds.view');
$app->post('/feeds/{id:[0-9]+}', '\Feeds\FeedsController:update')->setName('feeds.update');
