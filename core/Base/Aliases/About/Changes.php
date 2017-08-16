<?php

namespace Core\Base\Aliases\About;

use Core\Command\Alias;

class Changes extends Alias
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'changes';

    /**
     * {@inheritdoc}
     */
    protected $description = '{INHERIT_ABOUT_CHANGES}';

    /**
     * {@inheritdoc}
     */
    public $alias = 'about';

    /**
     * Default alias method.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function index()
    {
        $this->runCommand('changes');
    }
}
