<?php

namespace Core;

use Core\Config\Configuration;
use Discord\DiscordCommandClient;
use Discord\Parts\User\Game;
use \ReflectionMethod;

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
    const VERSION = '0.1';

    /**
     * Ctor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_env = new Environment();
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
    public function setGame($gameName)
    {
        $this->_game = $this->_discord->factory(Game::class, [
            'name' => $gameName,
        ]);
    }

    /**
     * Starts the bot
     *
     * @return void
     */
    public function start()
    {
        $this->_discord->on('ready', function($discord) {
            if (!is_null($this->_game)) {
                $discord->updatePresence($this->_game);
            }

            $msg = "\nBot is now online\n"
                . "Bot ID: {$discord->user->id}\n"
                . "Bot name: {$discord->user->username}\n";
            echo $msg;
        });

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
     * Returns the bot user object
     *
     * @return \Discord\Parts\User\User
     */
    public function getBotUser()
    {
        return $this->bot()->user;
    }

    /**
     * Registers the bot.
     *
     * @return void
     */
    public function registerDiscordBot()
    {
        $prefix_space = $this->_env->get('PREFIX_SPACE', false);
        $prefix = $this->_env->get('PREFIX', '');

        $prefix = ($prefix_space=='true') ? $prefix.' ' : $prefix;

        $opts = [
            'token' => $this->_env->get('TOKEN', ''),
            'prefix' => $prefix,
            'name' => $this->_env->get('NAME', ''),
            'description' => $this->_env->get('DESCRIPTION', 'AnubisBot is a Discord bot built in PHP').' // Version: '.self::VERSION,
        ];

        $this->_discord = new DiscordCommandClient($opts);

        $app = $this;
        $this->_discord->on('ready', function($discord) use ($app) {
            $app->registerCommands();
        });
    }

    /**
     * Registers all bot commands.
     *
     * @return void
     */
    public function registerCommands()
    {
        $commands = $this->_config->get('commands');

        foreach ($commands as $command) {
            $cmd = new $command($this);
            $desc = ($cmd->getDescription() == '') ? 'No description provided' : $cmd->getDescription();

            $app = $this;

            $this->_discord->registerCommand($cmd->getName(), function($message, $params) use ($cmd, $app) {
                $cmd->setMessage($message);
                $p = new Parameters($params);
                $method = $p->getMethod();

                if ($method == 'index') {
                    $app->call($cmd, $method, $p);
                } else {
                    if (method_exists(get_class($cmd), $method)) {
                        $app->call($cmd, $method, $p);
                    } else {
                        if (method_exists(get_class($cmd), 'index')) {
                            $parms = ['index'];
                            foreach ($params as $par) {
                                $parms[] = $par;
                            }
                            $p = new Parameters($parms);
                            $method = $p->getMethod();
                            $app->call($cmd, $method, $p);
                        } else {
                            echo "$method is not a valid command method!";
                        }
                    }
                }
            }, [
                'description' => $desc.'.',
            ]);
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
    public function call($class, $method, Parameters $params)
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
