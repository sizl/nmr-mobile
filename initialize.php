<?php

define('APP_ROOT', dirname(__FILE__));

require APP_ROOT . '/vendor/autoload.php';
require APP_ROOT . '/config/config.php';

//include environment specific configs
require APP_ROOT . '/config/' . ENVIRONMENT . '.php';

$app = new \Nmr\Application();
$app->getController($_SERVER["REQUEST_URI"])->run();