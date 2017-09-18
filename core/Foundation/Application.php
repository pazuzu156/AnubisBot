<?php

namespace Core\Foundation;

use Core\Command\Command;
use Core\Command\Parameters;
use Core\Utils\BotData;
use Core\Utils\Configuration;
use Core\Utils\File;
use Core\Wrappers\Logger;
use Core\Wrappers\Parts\Guild;
use Discord\DiscordCommandClient;
use Discord\Parts\User\Game;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;
use ReflectionMethod;

class Application
{
    /**
     * DiscordCommandClient instance.
     *
     * @var \Discord\DiscordCommandClient
     */
    private $_discord;

    /**
     * Environment instance.
     *
     * @var \Core\Foundation\Environment
     */
    private $_env;

    /**
     * Discord game instance.
     *
     * @var \Discord\Parts\User\Game
     */
    private $_game;

    /**
     * Configuration instance.
     *
     * @var \Core\Utils\Configuration
     */
    private $_config;

    /**
     * Bot version.
     *
     * @var string
     */
    const VERSION = '1.5.2';

    /**
     * List of current active commands.
     *
     * @var array
     */
    private $_commands;

    /**
     * Logger instance.
     *
     * @var \Core\Wrappers\LoggerWrapper
     */
    private $_logger;

    /**
     * Preset variables to parse in bot.
     *
     * @var array
     */
    private $_presets = ['NUMBER_OF_GUILDS', 'S', 'SHARD_ID', 'NUMBER_OF_SHARDS'];

    /**
     * Ctor.
     *
     * @param array $shards
     *
     * @return void
     */
    public function __construct(array $shards = [])
    {
        $this->_env = new Environment();
        $this->_logger = new Logger(env('NAME', ''));
        $this->_config = new Configuration();

        $this->registerDiscordBot($shards);
    }

    /**
     * Sets the bot's game.
     *
     * @param string $gameName
     *
     * @return void
     */
    public function setPresence($gameName)
    {
        $this->_logger->info('Setting bot\'s presence');
        $this->_game = $this->_discord->factory(Game::class, [
            'name' => $gameName,
            'type' => Game::TYPE_PLAYING,
        ]);
    }

    /**
     * Starts the bot.
     *
     * @return void
     */
    public function start()
    {
        $this->_logger->info('Starting DiscordPHP Loop');
        $app = $this;

        $this->_logger->info('Registering DiscordPHP\'s READY event');
        $this->_discord->on('ready', function ($discord) use ($app) {
            if (!is_null($this->_game)) {
                $ctx = $this;
                $presets = $this->_presets;
                $this->_game->name = preg_replace_callback('/\{([A-Z\_]+)\}/i', function ($m) use ($ctx, $presets, $discord) {
                    foreach ($presets as $preset) {
                        if ($m[1] == $preset) {
                            switch ($m[1]) {
                                case 'NUMBER_OF_GUILDS':
                                    if ($this->isSharded()) {
                                        return $ctx->numberOfGuilds(false);
                                    }

                                    return $ctx->numberOfGuilds();
                                case 'S':
                                    if ($this->isSharded()) {
                                        return ($ctx->numberOfGuilds(false) == 1) ? '' : 's';
                                    }

                                    return ($ctx->numberOfGuilds() == 1) ? '' : 's';
                                case 'SHARD_ID':
                                    if ($this->isSharded()) {
                                        return $discord->options['shardId'] + 1;
                                    }

                                    return 0;
                                case 'NUMBER_OF_SHARDS':
                                    if ($this->isSharded()) {
                                        return $discord->options['shardCount'];
                                    }

                                    return 0;
                            }
                        }
                    }
                }, $this->_game->name);

                $discord->updatePresence($this->_game);
            }

            $msg = "\nBot is now online\n"
                ."Bot ID: {$discord->user->id}\n"
                ."Bot name: {$discord->user->username}\n";

            if ($this->isSharded()) {
                $id = $this->_discord->options['shardId'] + 1;
                $msg .= "Shard ID: $id\n"
                    ."Shard Count: {$this->_discord->options['shardCount']}\n"
                    ."Guild Count on Current Shard: {$this->numberOfGuilds()}\n"
                    ."Total Guild Count: {$this->numberOfGuilds(false)}\n";
            } else {
                $msg .= "Guild Count: {$this->numberOfGuilds()}\n";
            }

            foreach (explode("\n", $msg) as $line) {
                if ($line !== '') {
                    $this->_logger->info($line);
                }
            }

            echo $msg;
        });

        $this->_discord->on(Event::GUILD_MEMBER_ADD, function ($member) use ($app) {
            $guild = new Guild($member->guild);
            $bannedUsers = $app->getBannedUsers($guild);
            $bannedUser = false;

            foreach ($bannedUsers as $bu) {
                if ($member->user->id == $bu) {
                    $app->banUser($member);
                    $bannedUser = true;
                }
            }

            if (!$bannedUser) {
                if ($guild->dataFile()->exists()) {
                    $dataFile = $guild->dataFile();

                    if ($dataFile->get('display_join_leave_msg')) {
                        if ($bsc = $dataFile->get('bot_spam_channel')) {
                            $channel = $guild->channels->get('id', $bsc['id']);
                            $msg = $dataFile->get('messages');

                            if ($msg && ($msg['join'] !== '')) {
                                $message = $msg['join'];
                            } else {
                                $message = 'Hello {USER}, welcome to **{GUILD}**. Please enjoy your stay! :smile:';
                            }

                            $message = preg_replace_callback('/\{([a-zA-Z]+)\}/i', function ($m) use ($member, $guild) {
                                switch (strtolower($m[1])) {
                                    case 'user':
                                        return $member;
                                        break;
                                    case 'guild':
                                        return $guild->name;
                                        break;
                                }
                            }, $message);

                            dump($message);

                            $channel->sendMessage($message);
                        }
                    }
                }
            }
        });

        $this->_discord->on(Event::GUILD_MEMBER_REMOVE, function ($member) use ($app) {
            $guild = new Guild($member->guild);
            $bannedUsers = $app->getBannedUsers($guild);
            $bannedUser = false;

            foreach ($bannedUsers as $bu) {
                if ($member->user->id == $bu) {
                    $app->banUser($member);
                    $bannedUser = true;
                }
            }

            if (!$bannedUser) {
                if ($guild->dataFile()->exists()) {
                    $dataFile = $guild->dataFile();

                    if ($dataFile->get('display_join_leave_msg')) {
                        if ($bsc = $dataFile->get('bot_spam_channel')) {
                            $channel = $guild->channels->get('id', $bsc['id']);
                            $msg = $dataFile->get('messages');

                            if ($msg && ($msg['leave'] !== '')) {
                                $message = $msg['leave'];
                            } else {
                                $message = 'User {USER} has left the server. Awe...';
                            }

                            $message = preg_replace_callback('/\{([a-zA-Z]+)\}/i', function ($m) use ($member) {
                                switch (strtolower($m[1])) {
                                    case 'user':
                                        return $member;
                                        break;
                                    break;
                                }
                            }, $message);

                            $channel->sendMessage($message);
                        }
                    }
                }
            }
        });

        $this->_logger->info('Executing DiscordPHP');
        $this->_discord->run();
    }

    /**
     * Returns the bot instance.
     *
     * @return \Discord\DiscordCommandClient
     */
    public function bot()
    {
        return $this->_discord;
    }

    /**
     * Returns the bot user object.
     *
     * @return \Discord\Parts\User\User
     */
    public function getBotUser()
    {
        return $this->bot()->user;
    }

    /**
     * Returns the current command list.
     *
     * @return array
     */
    public function getCommandList()
    {
        return $this->_commands;
    }

    /**
     * Gets the current logger instance.
     *
     * @return \Core\Wrappers\LoggerWrapper
     */
    public function logger()
    {
        return $this->_logger;
    }

    /**
     * Returns an array of banned users (for users not currently in server).
     *
     * @param \Core\Wrappers\Parts\Guild $guild
     *
     * @return array
     */
    public function getBannedUsers($guild)
    {
        if ($guild->dataFile()->exists()) {
            $data = $guild->dataFile()->getAsObject();

            if (isset($data->banned_users)) {
                return $data->banned_users;
            }
        }

        $dataFile = $guild->dataFile()->getAsArray();
        $dataFile['banned_users'] = [];
        $guild->dataFile()->write($dataFile);

        return $this->getBannedUsers();
    }

    /**
     * Handles banning users.
     *
     * @param \Core\Wrappers\Parts\Member $member
     * @param int                         $count
     *
     * @return \React\Promise\Promise
     */
    public function banUser($member, $count = 50)
    {
        return $member->ban($count);
    }

    /**
     * Removes a member from the auto-ban list.
     *
     * @param \Core\Wrappers\Parts\Guild $guild
     * @param \Discord\Parts\User\Member $member
     *
     * @return void
     */
    public function removeUserFromBanList($guild, Member $member)
    {
        $dataFile = $guild->dataFile()->getAsArray();

        for ($i = 0; $i < count($dataFile['banned_users']); $i++) {
            if ($dataFile['banned_users'][$i] == $member->user->id) {
                unset($dataFile['banned_users'][$i]);
            }
        }

        $guild->dataFile()->write($dataFile);
    }

    /**
     * Returns the branch for the git repo if the .git dir exists.
     * Returns master otherwise.
     *
     * @return string
     */
    public static function branch()
    {
        $head = base_path().'/.git/HEAD';

        if (File::exists($head)) {
            $exp = explode('/', File::get($head));

            return str_replace("\n", '', $exp[2]);
        }

        return 'master';
    }

    /**
     * Returns the bot's prefix and space if required.
     *
     * @return string
     */
    public function getPrefix()
    {
        $prefix = env('PREFIX', '!');

        if (env('PREFIX_SPACE', false)) {
            $prefix = " $prefix";
        }

        return $prefix;
    }

    /**
     * Shuts down the bot.
     *
     * @return void
     */
    public function shutdown()
    {
        $this->bot()->close();
        exit;
    }

    /**
     * Registers the bot.
     *
     * @param array $shards
     *
     * @return void
     */
    public function registerDiscordBot(array $shards = [])
    {
        $opts = [
            'token'          => $this->_env->get('TOKEN', ''),
            'prefix'         => $this->getPrefix(),
            'name'           => $this->_env->get('NAME', ''),
            'description'    => $this->_env->get('DESCRIPTION', 'AnubisBot is a Discord bot built in PHP').' // Version: '.self::VERSION,
            'discordOptions' => [
                'logging' => env('LOG_DISCORDPHP', true),
                'logger'  => $this->_logger->get(),
            ],
            'defaultHelpCommand' => false,
        ];

        if (!empty($shards)) {
            try {
                $opts['discordOptions']['shardId'] = $shards[0];
                $opts['discordOptions']['shardCount'] = $shards[1];
            } catch (\Exception $ex) {
                die('Invalid sharding!');
            }
        }

        $this->_logger->info('Registering DiscordPHP bot');
        $this->_discord = new DiscordCommandClient($opts);

        $this->_logger->info('Registering commands');
        $this->registerCommands();

        $this->_logger->info('Registering command aliases');
        $this->registerCommands(true); // Aliases
    }

    /**
     * Returns whether or not the bot is sharded.
     *
     * @return bool
     */
    public function isSharded()
    {
        return isset($this->_discord->options['shardId']);
    }

    /**
     * Registers all bot commands.
     *
     * @param bool $alias
     *
     * @return void
     */
    private function registerCommands($alias = false)
    {
        if ($alias) {
            $commands = $this->_config->get('aliases');
            $logstr = 'alias';
        } else {
            $commands = $this->_config->get('commands');
            $logstr = 'command';
        }

        foreach ($commands as $command) {
            $cmd = new $command($this);
            $descr = (!$cmd->getDescription()) ? 'No description provided' : $cmd->getDescription();
            $usage = (!$cmd->getUsage()) ? '' : $cmd->getUsage();

            $this->_logger->info("Registering $logstr {$cmd->getName()}");

            $app = $this;

            $desc = $cmd->parseDescription($descr);

            $command = $this->_discord->registerCommand($cmd->getName(), function ($message, $params) use ($cmd, $app) {
                $cmd->setMessage($message);
                $p = new Parameters(['method' => 'index', 'params' => $params]);

                if (method_exists(get_class($cmd), 'index')) {
                    $app->call($cmd, 'index', $p);
                } else {
                    echo "{$cmd->getname()} doesn't have [index] method and is not calling a sub command!";
                }
            }, [
                'description' => $cmd->parseDescription($desc, 'inherit').'.',
                'usage'       => $cmd->parseDescription($usage),
            ]);

            $methods = get_class_methods($cmd);
            $subCommandsArray = [];
            foreach ($methods as $m) {
                if ($m == '__construct') {
                    break;
                }

                if ($m != 'index') {
                    if ($m != 'getParentCommand') {
                        $subCommandsArray[] = $m;
                    }
                }
            }

            if ($alias) {
                $this->_discord->registerAlias($cmd->getName(), $cmd->alias);
            }

            foreach ($subCommandsArray as $subCommand) {
                $this->_logger->info("Registering sub command {$cmd->getName()} -> $subCommand");
                $methodReflection = new ReflectionMethod(get_class($cmd), $subCommand);
                $description = $cmd->getSubCommandDescription($cmd, $subCommand, $methodReflection);
                $command->registerSubCommand($subCommand, function ($message, $params) use ($cmd, $app, $subCommand) {
                    $cmd->setMessage($message);
                    $p = new Parameters(['method' => $subCommand, 'params' => $params]);
                    $method = $p->getMethod();

                    $app->call($cmd, $method, $p);
                }, [
                    'description' => $description,
                ]);
            }

            $this->_commands[$cmd->getName()] = [
                'class'        => $cmd,
                'sub_commands' => $subCommandsArray,
                'is_alias'     => $alias,
            ];
        }
    }

    /**
     * Makes the command call.
     *
     * @param mixed                    $class
     * @param string                   $method
     * @param \Core\Command\Parameters $params
     *
     * @return void
     */
    private function call($class, $method, Parameters $params)
    {
        $methodInfo = new ReflectionMethod(get_class($class), $method);
        if ($params->count()) {
            if (count($methodInfo->getParameters()) > 0) {
                $class->$method($params);
            } else {
                $class->$method();
            }
        } else {
            if (count($methodInfo->getParameters()) > 0) {
                $class->$method(new Parameters([$method]));
            } else {
                $class->$method();
            }
        }
    }

    /**
     * Returns the number of guilds the bot is registered to.
     *
     * @param bool $calculate
     *
     * @return int
     */
    public function numberOfGuilds($calculate = true)
    {
        if ($calculate) {
            $i = 0;

            foreach ($this->bot()->guilds as $guild) {
                $g = new Guild($guild);

                $dataFile = $g->dataFile()->getAsArray();
                $dataFile['guild_name'] = $g->name;

                $g->dataFile()->write($dataFile);
                $i++;
            }

            return $i;
        } else {
            $shards = BotData::get('shards');

            if ($shards !== false) {
                $count = 0;
                foreach ($shards as $shard) {
                    $count += $shard['guild_count'];
                }

                return $count;
            } else {
                return $this->numberOfGuilds();
            }
        }
    }
}
