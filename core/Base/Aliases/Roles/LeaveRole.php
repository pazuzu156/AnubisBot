<?php

namespace Core\Base\Aliases\Roles;

use Core\Command\Alias;
use Core\Command\Parameters;

class LeaveRole extends Alias
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'leaverole';

    /**
     * {@inheritdoc}
     */
    protected $description = '{INHERIT_ROLES_LEAVE}';

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
        $this->runCommand('leave', $p);
    }
}
