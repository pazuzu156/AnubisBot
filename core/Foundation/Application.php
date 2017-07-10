<?php

namespace Core\Foundation;

use Core\Command\Command;
use Core\Command\Parameters;
use Core\Utils\Configuration;
use Core\Wrappers\File;
use Core\Wrappers\Logger;
use Core\Wrappers\Parts\Guild;
use Core\Wrappers\Parts\Member;
use Discord\DiscordCommandClient;
use Discord\Parts\User\Game;
use Discord\Parts\User\Member as DMember;
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
    const VERSION = '1.3.1';

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
     * Ctor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_env = new Environment();
        $this->_logger = new Logger(env('NAME', ''));
        $this->_config = new Configuration();

        $this->registerDiscordBot();
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
                $discord->updatePresence($this->_game);
            }

            $msg = "\nBot is now online\n"
                ."Bot ID: {$discord->user->id}\n"
                ."Bot name: {$discord->user->username}\n";

            foreach (explode("\n", $msg) as $line) {
                if ($line !== '') {
                    $this->_logger->info($line);
                }
            }

            echo $msg;
        });

        $this->_logger->info('Registering DiscordPHP CLOSED event');
        $this->_discord->on('closed', function ($discord) {
            if (File::exists(storage_path().'/bot_online')) {
                File::delete(storage_path().'/bot_online');
            }
            exit;
        });

        $this->_discord->on(Event::GUILD_MEMBER_ADD, function ($member) use ($app) {
            foreach ($app->bot()->guilds as $guild) {
                $guild = new Guild($guild);
                if ($guild->id == $member->guild_id) {
                    $member = new Member($guild, $member);
                    $bannedUsers = $app->getBannedUsers($guild);
                    $bannedUser = false;

                    foreach ($bannedUsers as $bu) {
                        if ($member->id == $bu) {
                            $app->banUser($member);
                            $bannedUser = true;
                        }
                    }

                    // only display if enabled. If enabled, don't show if a user is auto-banned
                    if (env('DISPLAY_USER_JOIN_LEAVE', true) && !$bannedUser) {
                        if (File::exists($guild->dataFile())) {
                            $dataFile = json_decode(File::get($guild->dataFile()), true);

                            if (isset($dataFile['bot_spam_channel'])) {
                                $channel = $guild->channels->get('id', $dataFile['bot_spam_channel']['id']);
                                if (!isset($dataFile['messages']['join'])
                                    || $dataFile['messages']['join'] == '') {
                                    $message = 'User {USER} has joined the server. Welcome! :smile:';
                                } else {
                                    $message = $dataFile['messages']['join'];
                                }

                                $message = preg_replace_callback('/\{([a-zA-Z]+)\}/i', function ($m) use ($member) {
                                    switch (strtolower($m[1])) {
                                        case 'user':
                                        return $member;
                                        break;
                                    }
                                }, $message);

                                $channel->sendMessage($message);
                            }
                        }
                    }
                }
            }
        });

        if (env('DISPLAY_USER_JOIN_LEAVE', true)) {
            $this->_discord->on(Event::GUILD_MEMBER_REMOVE, function ($member) use ($app) {
                foreach ($app->bot()->guilds as $guild) {
                    $guild = new Guild($guild);
                    if ($guild->id == $member->guild_id) {
                        $bannedUsers = $app->getBannedUsers($guild);
                        $removeUser = false;

                        foreach ($bannedUsers as $bu) {
                            if ($member->user->id == $bu) {
                                $removeUser = true;
                            }
                        }

                        dump($removeUser);

                        if ($removeUser) {
                            $app->removeUserFromBanList($guild, $member);
                        } else {
                            if (File::exists($guild->dataFile())) {
                                $dataFile = json_decode(File::get($guild->dataFile()), true);

                                if (isset($dataFile['bot_spam_channel'])) {
                                    $channel = $guild->channels->get('id', $dataFile['bot_spam_channel']['id']);
                                    if (!isset($dataFile['messages']['leave'])
                                        || $dataFile['messages']['leave'] == '') {
                                        $message = 'User {USER} has left the server. Awe...';
                                    } else {
                                        $message = $dataFile['messages']['leave'];
                                    }

                                    $message = preg_replace_callback('/\{([a-zA-Z]+)\}/i', function ($m) use ($member) {
                                        switch (strtolower($m[1])) {
                                            case 'user':
                                            return $member;
                                            break;
                                        }
                                    }, $message);

                                    $channel->sendMessage($message);
                                }
                            }
                        }
                    }
                }
            });
        }

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
        if (File::exists($guild->dataFile())) {
            return json_decode(File::get($guild->dataFile()))->banned_users;
        }

        File::writeAsJson($guild->dataFile(), [
            'banned_users' => [],
        ]);

        return $this->getBannedUsers();
    }

    /**
     * Handles banning users.
     *
     * @param \Core\Wrappers\Parts\Member $member
     * @param int                         $count
     * @param bool                        $remove
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
    public function removeUserFromBanList($guild, DMember $member)
    {
        $dataFile = json_decode(File::get($guild->dataFile()), true);

        for ($i = 0; $i < count($dataFile['banned_users']); $i++) {
            if ($dataFile['banned_users'][$i] == $member->user->id) {
                unset($dataFile['banned_users'][$i]);
            }
        }

        File::writeAsJson($guild->dataFile(), $dataFile);
    }

    /**
     * Registers the bot.
     *
     * @return void
     */
    private function registerDiscordBot()
    {
        $prefix_space = $this->_env->get('PREFIX_SPACE', false);
        $prefix = $this->_env->get('PREFIX', '');

        $prefix = ($prefix_space == 'true') ? $prefix.' ' : $prefix;

        $opts = [
            'token'          => $this->_env->get('TOKEN', ''),
            'prefix'         => $prefix,
            'name'           => $this->_env->get('NAME', ''),
            'description'    => $this->_env->get('DESCRIPTION', 'AnubisBot is a Discord bot built in PHP').' // Version: '.self::VERSION,
            'discordOptions' => [
                'logging' => env('LOG_DISCORDPHP', true),
                'logger'  => $this->_logger->get(),
            ],
            'defaultHelpCommand' => false,
        ];

        $this->_logger->info('Registering DiscordPHP bot');
        $this->_discord = new DiscordCommandClient($opts);

        $this->_logger->info('Registering commands');
        $this->registerCommands();

        $this->_logger->info('Registering command aliases');
        $this->registerCommands(true); // Aliases
    }

    /**
     * Registers all bot commands.
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
            $desc = ($cmd->getDescription() == '') ? 'No description provided' : $cmd->getDescription();

            $this->_logger->info("Registering $logstr {$cmd->getName()}");

            $app = $this;

            $desc = $cmd->parseDescription($desc);

            $command = $this->_discord->registerCommand($cmd->getName(), function ($message, $params) use ($cmd, $app) {
                $cmd->setMessage($message);
                $p = new Parameters(['method' => 'index', 'params' => $params]);

                if (method_exists(get_class($cmd), 'index')) {
                    $app->call($cmd, 'index', $p);
                } else {
                    echo "{$cmd->getname()} doesn't have [index] method and is not calling a sub command!";
                }
            }, [
                'description' => $cmd->parseDescription($desc, true).'.',
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
}
