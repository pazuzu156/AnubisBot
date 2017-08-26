<?php

namespace Core\Base\Commands;

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
     * Shuts down the bot.
     *
     * @example {COMMAND} shutdown
     *
     * @return void
     */
    public function shutdown()
    {
        if ($this->can('administrator') && $this->isBotOwner()) {
            $bot = $this->app->bot();
            $logger = $this->logger;
            $this->channel->sendMessage('Bringing the bot offline...')->then(function ($msg) use ($bot, $logger) {
                $logger->info('Shutting down bot');
                $bot->close();
            });
        } else {
            $this->message->reply('You do not have permission to shutdown the bot!');
        }
    }
}
