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
    protected $usage = '{COMMAND} [LIMIT]';

    /**
     * {@inheritdoc}
     */
    public $alias = 'msg';

    /**
     * Default alias method.
     *
     * @param \Core\Commands\Parameters $p
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
     * @example {COMMAND} <USER> [LIMIT]
     *
     * @param \Core\Commands\Parameters $p
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
     * @example {COMMAND} [LIMIT]
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function bot(Parameters $p)
    {
        $this->runCommand('prunebot', $p);
    }
}
