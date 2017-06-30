<?php

namespace Core\Base\Aliases\Roles;

use Core\Command\Alias;
use Core\Command\Parameters;

class JoinRole extends Alias
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'joinrole';

    /**
     * {@inheritdoc}
     */
    protected $description = '{INHERIT_ROLES_JOIN}';

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
        $this->runCommand('join', $p);
    }
}
