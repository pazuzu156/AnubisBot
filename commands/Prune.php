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
    protected $description = 'Prune messages. (Default limit: 50)';

    /**
     * Default message log limit.
     *
     * @var int
     */
    const DEFAULT_LIMIT = 50;

    /**
     * Prune all messages limited by limit.
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
     * Prune all user messages limited by limit.
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
     * Prune all bot messages limited by limit.
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
