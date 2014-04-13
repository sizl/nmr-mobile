<?php

define('APP', 'Website');

//IMPORTANT!!! config should not be committed!
require '../../../config/config.php';

require APP_ROOT . '/vendor/autoload.php';

//Environment Specific Config
require APP_ROOT . '/config/' . ENVIRONMENT . '.php';

$app = new \Nmr\Application();
$app->getController()->run();