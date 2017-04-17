<?php

namespace App\Commands;

use Core\Command;

class TestCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'testcommand';

    /**
     * {@inheritdoc}
     */
    protected $description = 'This is an example command';

    /**
     * Ping the bot, get a response as pong!
     *
     * @return void
     */
    public function ping()
    {
        $this->message->reply('pong!');
    }
}
