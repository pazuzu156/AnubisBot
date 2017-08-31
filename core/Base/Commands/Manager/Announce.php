<?php

namespace Core\Base\Commands\Manager;

use Core\Command\Command;
use Core\Command\Parameters;

// THIS COMMAND IS A WORK IN PROGRESS

class Announce extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'announce';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Sends an announcement to all guilds if they\'ve specified a bot spam channel';

    /**
     * {@inheritdoc}
     */
    protected $usage = '{COMMAND} [MESSAGE]';

    /**
     * {@inheritdoc}
     */
    protected $hidden = true;

    /**
     * Default command method.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        // Default command method
    }

    // Place your methods here
}
