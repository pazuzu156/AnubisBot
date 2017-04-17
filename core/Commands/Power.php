<?php

namespace Core\Commands;

use Core\Command;

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
     * @return void
     */
    public function shutdown()
    {
        if ($this->can('administrator')) {
            $bot = $this->app->bot();
            $logger = $this->logger;
            $this->channel->sendMessage('Brining the bot offline...')->then(function ($msg) use ($bot, $logger) {
                $logger->info('Shutting down bot');
                $bot->close();
            });
            // tsleep(1);
            // $this->app->bot()->close();
        } else {
            $this->message->reply('You do not have permission to shutdown the bot!');
        }
    }
}
