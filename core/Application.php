<?php

namespace Core;

use Curl\Curl;
use Discord\DiscordCommandClient;
use Discord\Parts\Channel\Channel;
use Discord\Parts\User\Game;
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
     * @var \Core\Environment
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
     * @var \Core\Config\Configuration
     */
    private $_config;

    /**
     * Bot version.
     *
     * @var string
     */
    const VERSION = '0.3.2';

    /**
     * List of current active commands.
     *
     * @var array
     */
    private $_commands;

    /**
     * Logger instance.
     *
     * @var \Core\Logger
     */
    private $_logger;

    /**
     * Ctor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_logger = new Logger(env('NAME', ''));
        $this->_env = new Environment($this->_logger);
        $this->_config = new Configuration();

        $this->registerDiscordBot();
    }

    /**
     * Sets the bot's game.
     *
     * @param string $gamaeName
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
    public function start($startMessage = true)
    {
        $this->_logger->info('Starting DiscordPHP Loop');
        $app = $this;

        $this->_logger->info('Registering DiscordPHP\'s READY event');
        $this->_discord->on('ready', function ($discord) use ($startMessage, $app) {
            if (!is_null($this->_game)) {
                $discord->updatePresence($this->_game);
            }

            if ($startMessage) {
                $app->logger()->info('Getting changelog to display to given bot-spam channel');
                $channel = $discord->factory(Channel::class, [
                    'id'   => env('BOTSPAM_CHANNEL_ID', ''),
                    'type' => 'text',
                ]);

                $app->logger()->info('Opening up cURL connection');
                $curl = new Curl();
                $curl->get('https://cdn.kalebklein.com/anubisbot/changes.php');

                $message = env('NAME', '').' is back online! Here are the new changes this update:'."\n\n";

                $app->logger()->info('Parsing changelog');
                $cmd = new Command();
                $message .= $cmd->parseDescription($curl->response);

                $app->logger()->info('Sending changelog');
                $channel->sendMessage($message);
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
            if (file_exists(base_path().'/bot_online')) {
                unlink(base_path().'/bot_online');
            }
            exit;
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
     * @return \Core\Logger
     */
    public function logger()
    {
        return $this->_logger;
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
            'token'       => $this->_env->get('TOKEN', ''),
            'prefix'      => $prefix,
            'name'        => $this->_env->get('NAME', ''),
            'description' => $this->_env->get('DESCRIPTION', 'AnubisBot is a Discord bot built in PHP').' // Version: '.self::VERSION,
            'discordOptions' => [
                'logging' => env('LOG_DISCORDPHP', true),
                'logger' => $this->_logger->get(),
            ],
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
                    $subCommandsArray[] = $m;
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
            ];
        }
    }

    /**
     * Makes the command call.
     *
     * @param mixed            $class
     * @param string           $method
     * @param \Core\Parameters $params
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
