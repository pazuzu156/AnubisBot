<?php

namespace Core\Base\Commands\Manager;

use Core\Command\Command;

class Power extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'power';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Handles the bots power';

    /**
     * {@inheritdoc}
     */
    protected $hidden = true;

    /**
     * Shuts down the bot.
     *
     * @example {COMMAND} shutdown
     *
     * @return void
     */
    public function index()
    {
        if ($this->can('administrator') && $this->isBotOwner()) {
            $app = $this->app;
            $logger = $this->logger;
            $this->channel->sendMessage('Bringing the bot offline...')->then(function ($msg) use ($app, $logger) {
                $logger->info('Shutting down bot');
                $app->shutdown();
            });
        } else {
            $this->message->reply('You do not have permission to shutdown the bot!');
        }
    }
}
