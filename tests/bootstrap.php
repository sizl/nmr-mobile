<?php

define('APP_ROOT', dirname(__DIR__));

define('MODULE', 'mobile');

require APP_ROOT . '/vendor/autoload.php';
require APP_ROOT . '/config/config.php';

define('ENVIRONMENT', 'development');
define('MODULE_PATH', APP_ROOT . '/modules/' . MODULE);

