<?php

define('APP_ROOT', __DIR__.'/../..');

require APP_ROOT . '/vendor/autoload.php';

ini_set('display_errors', '1');

date_default_timezone_set('America/Los_Angeles');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

define('ENVIRONMENT', 'development');
define('MODULE_PATH', APP_ROOT . '/modules/' . MODULE);

$slim = new \Slim\Slim([
	'mode' => ENVIRONMENT,
	'debug'=> (ENVIRONMENT == 'development'),
	'view' => new Slim\Views\Twig(),
	'templates.path' => MODULE_PATH. '/views',
]);

$app = new \Nmr\Application($slim);
$app->configureRoutes()
	->run();