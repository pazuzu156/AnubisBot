<?php

require_once __DIR__.'/vendor/autoload.php';

date_default_timezone_set('America/New_York');
ini_set('memory_limit', '250M');

$GLOBALS['START_TIME'] = time();

use Core\Foundation\Application;

$app = new Application();
$app->setPresence(env('DEFAULT_BOT_PRESENCE', ''));

$app->logger()->info('Booting up bot application');
$app->start();
