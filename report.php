<?php

/**
 * This script is used for updating DiscordBots
 * if your bot is on there. It uses their API to update
 * server and shard count.
 *
 * There are 2 arrays in the `post` function, comment
 * one out depending on the use of sharding or not.
 *
 * Sharding requires the existance of the file
 * `storage/data/botinfo.json`
 */
require_once __DIR__.'/vendor/autoload.php';

use Core\Foundation\Environment;
use Core\Utils\BotData;
use GuzzleHttp\Client as Guzzle;

$shards = BotData::get('shards');

if ($shards !== false) {
    foreach ($shards as $shard) {
        post($shard, count($shards));
    }
} else {
    // post() 2nd parameter is server count, update accordingly
    post(null, 0);
}

/**
 * Sends an update to the Discord Bot List API.
 *
 * @param array $shard       - The shard info
 * @param int   $shard_count - Number of shards (or server count if no sharding)
 *
 * @return void
 */
function post($shard, $shard_count)
{
    $env = new Environment();

    $id = 'DISCORD_BOT_ID'; // Your bot's ID goes here
    $g = new Guzzle();

    // This array is for sharding
    $data = [
        'shard_id'     => $shard['id'],
        'server_count' => $shard['guild_count'],
        'shard_count'  => $shard_count,
    ];

    // This array is for no sharding
    // uses $shard_count to get server cound
    // $data = [
    //     'server_count' => $shard_count,
    // ];

    $res = $g->request('POST', 'https://discordbots.org/api/bots/'.$id.'/stats', [
        'headers' => [
            'Authorization' => $env->get('DB_TOKEN'),
        ],
        'form_params' => $data,
    ]);

    $res = $g->request('GET', 'https://discordbots.org/api/bots/'.$id.'/stats');
    dump($res->getBody()->getContents());
}
