<?php

namespace Commands;

use Core\Command;
use Core\Parameters;

class About extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'about';

    /**
     * {@inheritdoc}
     */
    protected $description = '';

    /**
     * Default command method.
     *
     * @return void
     */
    public function index()
    {
        // Default command method
        $name = env('NAME', '');
        $version = version();

        $this->channel->sendMessage("$name is a bot written in PHP. Current version: v$version");
    }

    /**
     * Gives the GitHub link to the source code.
     *
     * @return void
     */
    public function source()
    {
        $this->channel->sendMessage('https://github.com/pazuzu156/anubisbot');
    }
}
