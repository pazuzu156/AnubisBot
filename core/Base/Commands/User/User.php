<?php

namespace Core\Base\Commands\User;

use Core\Command\Command;
use Core\Command\Parameters;

class User extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'user';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Manage the server\'s users';

    /**
     * Default command method.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        dump($this);
    }
}
