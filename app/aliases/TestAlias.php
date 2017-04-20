<?php

namespace App\Aliases;

use Core\Command\Alias;
use Core\Command\Parameters;

class TestAlias extends Alias
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'ping';

    /**
     * {@inheritdoc}
     */
    protected $description = '{INHERIT_TESTCOMMAND_PING}';

    /**
     * {@inheritdoc}
     */
    public $alias = 'testcommand';

    /**
     * Default alias method.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        $this->runCommand('ping', $p);
    }
}
