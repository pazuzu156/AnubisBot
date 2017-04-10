<?php

namespace Core;

class Command
{
    /**
     * The command's name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The command's description.
     *
     * @var string
     */
    protected $description = '';

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
     * The command's Author instance.
     *
     * @var \Discord\Parts\User\Member
     */
    protected $author;

    /**
     * Ctor.
     *
     * @return void
     */
    public function __construct()
    {
        if ($this->name == '') {
            throw new \RuntimeException('You command MUST have a name!');
        }
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
    }
}
