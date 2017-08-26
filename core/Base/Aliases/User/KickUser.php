<?php

namespace Core\Base\Aliases\User;

use Core\Command\Alias;
use Core\Command\Parameters;

class KickUser extends Alias
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'kick';

    /**
     * {@inheritdoc}
     */
    protected $description = '{INHERIT_USER_KICK}';

    /**
     * {@inheritdoc}
     */
    protected $usage = '{COMMAND} <USER>';

    /**
     * {@inheritdoc}
     */
    public $alias = 'user';

    /**
     * Default alias method.
     *
     * @param \Core\Command\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        $this->runCommand('kick', $p);
    }
}
