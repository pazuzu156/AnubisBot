<?php

namespace App\Aliases;

use Core\Alias;
use Core\Parameters;

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
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        $this->runCommand('ping', $p);
    }
}
