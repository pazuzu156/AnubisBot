<?php

namespace Core\Base\Aliases\User;

use Core\Command\Alias;
use Core\Command\Parameters;

class BanUser extends Alias
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'ban';

    /**
     * {@inheritdoc}
     */
    protected $description = '{INHERIT_USER_BAN}';

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
        $this->runCommand('ban', $p);
    }
}
