<?php

namespace Core\Base\Aliases;

use Core\Command\Alias;
use Core\Command\Parameters;

class Prune extends Alias
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'prune';

    /**
     * {@inheritdoc}
     */
    protected $description = '{INHERIT_MSG_PRUNE}';

    /**
     * {@inheritdoc}
     */
    public $alias = 'msg';

    /**
     * Default alias method.
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        $this->runCommand('prune', $p);
    }

    /**
     * {INHERIT_MSG_PRUNEUSER}.
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function user(Parameters $p)
    {
        $this->runCommand('pruneuser', $p);
    }

    /**
     * {INHERIT_MSG_PRUNEBOT}.
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function bot(Parameters $p)
    {
        $this->runCommand('prunebot', $p);
    }
}
