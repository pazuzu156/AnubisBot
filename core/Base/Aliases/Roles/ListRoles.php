<?php

namespace Core\Base\Aliases\Roles;

use Core\Command\Alias;
use Core\Command\Parameters;

class ListRoles extends Alias
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'listroles';

    /**
     * {@inheritdoc}
     */
    protected $description = '{INHERIT_ROLES}';

    /**
     * {@inheritdoc}
     */
    public $alias = 'roles';

    /**
     * Default alias method.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        $this->runCommand('index', $p);
    }
}
