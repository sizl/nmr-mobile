<?php

require '../vendor/autoload.php';

//IMPORTANT!!! config should not be committed!
require '../config/config.php';

//Environment Specific Config
require '../config/' . ENVIRONMENT . '.php';

$app = new \Nmr\Application();
$app->getController()->run();