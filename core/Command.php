<?php

namespace Core;

use Discord\Parts\Channel\Channel;
use ReflectionMethod;

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

    private $_presets = ['PREFIX', 'NAME'];

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
     * Parses a command description.
     *
     * @param string $description
     *
     * @return string
     */
    public function parseDescription($description)
    {
        $presets = $this->_presets;
        
        $description = preg_replace_callback('/\{([a-zA-Z]+)\}/i', function ($m) use ($presets) {
            foreach ($presets as $preset) {
                dump($m[1]);
                if ($m[1] == $preset) {
                    switch($preset) {
                        case 'PREFIX':
                        $prefix = env('PREFIX', '!');
                        if (env('PREFIX_SPACE', false)) {
                            $prefix = $prefix.' ';
                        }

                        return $prefix;
                        break;
                        case 'NAME':
                        return env('NAME', '');
                        break;
                    }
                }
            }

            return $m[0];
        }, $description);

        return $description;
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
     * Sets the message instance.
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

    /**
     * Extrapolates the sub command description from it's method's docblock.
     *
     * @param \Core\Command     $command
     * @param string            $subCommand
     * @param \ReflectionMethod $reflection
     *
     * @return string
     */
    public function getSubCommandDescription(Command $command, $subCommand, ReflectionMethod $reflection)
    {
        $doc = $reflection->getDocComment();

        $exp = explode("\r\n", $doc);
        if (count($exp) == 1) {
            $exp = explode("\n", $doc);
        }

        $lines = [];
        $desc = '';
        if (count($exp) > 1) {
            foreach ($exp as $line) {
                $line = ltrim($line, '* ');

                $fc = substr($line, 0, 1);

                if (preg_match('/[a-zA-Z]/i', $fc)) {
                    $line = $this->parseDescription($line);
                    $lines[] = $line;
                }
            }
        }

        if (count($lines) > 0) {
            $desc = implode(' ', $lines);
        }

        return ($desc == '') ? 'No description provided.' : $desc;
    }
}
