<?php

namespace Core;

use Discord\Parts\Channel\Channel;

class Command
{
    /**
     * The name of your command to register with Discord.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The command's description to serve in the help.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Application instance.
     *
     * @var \Core\Application
     */
    protected $app;

    /**
     * The command's Message instance.
     *
     * @var \Discord\Parts\Channel\Message
     */
    protected $message;

    /**
     * The command's Channel instance.
     *
     * @var \Discord\Parts\Channel\Channel
     */
    protected $channel;

    /**
     * The command's Guild instance.
     *
     * @var \Discord\Pard\Channel\Guild
     */
    protected $guild;

    /**
     * The command's Author instance.
     *
     * @var \Discord\Parts\User\Member
     */
    protected $author;

    /**
     * Permissions instance.
     *
     * @var \Core\Permissions
     */
    protected $permissions;

    /**
     * Ctor.
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        if ($this->name == '') {
            throw new \RuntimeException('You command MUST have a name!');
        }

        $this->app = $app;
        $this->permissions = new Permissions();
    }

    /**
     * Gets the command's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the commands description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the message instance
     *
     * @param \Discord\Parts\Channel\Message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
        $this->channel = $this->message->channel;
        $this->author = $this->message->author;
        $this->guild = $this->channel->guild;
    }
}
