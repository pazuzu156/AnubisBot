<?php

require_once __DIR__.'/vendor/autoload.php';

date_default_timezone_set('America/New_York');
ini_set('memory_limit', '250M');

$GLOBALS['START_TIME'] = time();

use Core\Foundation\Application;
use Core\Wrappers\FileSystemWrapper as File;

$app = new Application();
$app->setPresence(env('DEFAULT_BOT_PRESENCE', ''));

if (isset($bool) || is_null($bool)) {
    if (env('DEBUG', false) !== true) {
        $bool = true;
    } else {
        $bool = false;
    }
}

$app->logger()->info('Making sure bot tells the world it\'s online');
if (File::exists(storage_path().'/bot_online') && !env('DEBUG', false)) {
    File::write(storage_path().'/bot_online');
}

$app->logger()->info('Booting up bot application');
$app->start($bool);
