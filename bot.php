<?php

date_default_timezone_set('America/New_York');

ini_set('memory_limit', '250M');

$GLOBALS['START_TIME'] = time(); // Uptime tacking start

require_once __DIR__.'/vendor/autoload.php';

$app = new Core\Application();

// Set game presence
$app->setGame('God of the Underworld');

if (!isset($bool) || is_null($bool)) {
    $bool = true;
}

if (!file_exists(__DIR__.'/bot_online')) {
    $file = fopen(__DIR__.'/bot_online', 'w');
    fwrite('online', $file);
    fclose($file);
}

$app->start($bool);
