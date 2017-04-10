<?php

date_default_timezone_set('America/New_York');

$GLOBALS['START_TIME'] = time(); // Uptime tacking start

require_once __DIR__.'/vendor/autoload.php';

$app = new Core\Application();

// Set game presence
$app->setGame('Testing');

// run the bot
$app->start();
