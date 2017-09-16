<?php

require_once __DIR__.'/vendor/autoload.php';

date_default_timezone_set('America/New_York');
ini_set('memory_limit', '250M');

$GLOBALS['START_TIME'] = time();

use Core\Foundation\Application;

/**
 * Shards array has 2 values, id, and count.
 * index 0 is the id, and index 1 is the count.
 *
 * if you need 2 shards, then first shard id is 0, and second is 1
 *
 * run an instance with [0, 2] and another with [1, 2]
 * this will run on shards 1 and 2
 */
$shards = [0, 1]; // define shards here

$app = new Application($shards);
$app->setPresence(env('DEFAULT_BOT_PRESENCE', ''));

$app->logger()->info('Booting up bot application');
$app->start();
