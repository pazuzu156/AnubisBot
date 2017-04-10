<?php

namespace Core;

use Core\Config\Configuration;
use Discord\DiscordCommandClient;
use Discord\Parts\User\Game;

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
    public static $VERSION = '0.1';

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
        });

        $this->_discord->run();
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

        $prefix = ($prefix_space=='true') ? $prefix.' ' : $prefix;

        $opts = [
            'token' => $this->_env->get('TOKEN', ''),
            'prefix' => $prefix,
            'name' => $this->_env->get('NAME', ''),
            'description' => $this->_env->get('DESCRIPTION', 'AnubisBot is a Discord bot built in PHP').' // Version: '.self::$VERSION,
        ];

        $this->_discord = new DiscordCommandClient($opts);

        $this->registerCommands();
    }

    /**
     * Registers all bot commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $commands = $this->_config->get('commands');

        foreach ($commands as $command) {
            $cmd = new $command();
            $desc = ($cmd->getDescription() == '') ? 'No description provided' : $cmd->getDescription();

            $this->_discord->registerCommand($cmd->getName(), function($message, $params) use ($cmd) {
                $cmd->setMessage($message);
                if (!empty($params)) {
                    $method = $params[0];

                    if (method_exists(get_class($cmd), $method)) {
                        if (count($params) == 1) {
                            $cmd->$method();
                        } else {
                            array_shift($params);
                            $cmd->$method($params);
                        }
                    } else {
                        echo "$method is not a valid command method!";
                    }
                } else {
                    if (method_exists(get_class($cmd), 'index')) {
                        if (count($params) > 0) {
                            $cmd->index($params);
                        } else {
                            $cmd->index();
                        }
                    } else {
                        echo "Your command isn't calling a method, nor does it have index defined!";
                    }
                }
            }, [
                'description' => $desc.'.',
            ]);
        }
    }
}
