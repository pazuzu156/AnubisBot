<?php

namespace Core\Base\Aliases\About;

use Core\Command\Alias;

class Uptime extends Alias
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'uptime';

    /**
     * {@inheritdoc}
     */
    protected $description = '{INHERIT_ABOUT_UPTIME}';

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
        $this->runCommand('uptime');
    }
}
