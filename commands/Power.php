<?php

namespace Commands;

use Core\Command;
use Core\Parameters;

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
        if($this->can('administrator')) {
            $this->app->bot()->close();
        } else {
            $this->message->reply('You do not have permission to shutdown the bot!');
        }
    }
}
