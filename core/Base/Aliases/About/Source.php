<?php

namespace Core\Base\Aliases\About;

use Core\Command\Alias;

class Source extends Alias
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'source';

    /**
     * {@inheritdoc}
     */
    protected $description = '{INHERIT_ABOUT_SOURCE}';

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
        $this->runCommand('source');
    }
}
