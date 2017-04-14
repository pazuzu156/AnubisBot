<?php

namespace Aliases;

use Core\Alias;
use Core\Parameters;

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
     * Default command method.
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        $this->runCommand('msg', 'prune', $p);
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
        $this->runCommand('msg', 'pruneuser', $p);
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
        $this->runCommand('msg', 'prunebot', $p);
    }
}
