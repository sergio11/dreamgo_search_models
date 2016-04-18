<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

require __DIR__ . '/../resources/config/prod.php';

require __DIR__ . '/../src/app.php';
//include utils functions.
require __DIR__ . '/../src/App/inc/functions.inc.php';

$app['http_cache']->run();
