<?php

namespace Core\Base\Commands\Info;

use Core\Command\Command;
use Core\Command\Parameters;
use GuzzleHttp\Client as Guzzle;

class Changes extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'changes';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Gets the latest changelog';

    /**
     * {@inheritdoc}
     */
    protected $usage = '{COMMAND} changes';

    /**
     * {@inheritdoc}
     */
    protected $hidden = false;

    /**
     * Default command method.
     *
     * @return void
     */
    public function index()
    {
        $client = new Guzzle();
        $response = $client->get('https://api.kalebklein.com/anubisbot/changes');

        $content = $response->getBody()->getContents();

        $this->channel->sendMessage($content);
    }
}
