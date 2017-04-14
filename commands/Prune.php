<?php

namespace Commands;

use Core\Command;
use Core\Parameters;

class Prune extends Command
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
     * Default command method.
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        $this->alias('msg', 'prune', $p);
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
        $this->alias('msg', 'pruneuser', $p);
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
        $this->alias('msg', 'prunebot', $p);
    }
}
