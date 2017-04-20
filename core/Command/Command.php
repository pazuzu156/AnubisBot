<?php

namespace Core\Command;

use Core\Foundation\Application;
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
     * @var \Core\Foundation\Application
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
     * @var \Core\Command\Permissions
     */
    protected $permissions;

    /**
     * Logger instance.
     *
     * @var \Core\Wrappers\LoggerWrapper
     */
    protected $logger;

    /**
     * Preset variables to parse in command descriptions.
     *
     * @var array
     */
    private $_presets = ['PREFIX', 'NAME', 'VERSION'];

    /**
     * Variable regex array.
     *
     * @var array
     */
    private $_varRegex = [
        'default' => '/\{([a-zA-Z]+)\}/i',
        'inherit' => '/\{(INHERIT)\_([A-Z\_]+)\_([A-Z\_]+)\}/i',
    ];

    /**
     * Ctor.
     *
     * @return void
     */
    public function __construct(Application $app = null)
    {
        if (!is_null($app)) {
            if ($this->name == '') {
                throw new \RuntimeException('You command MUST have a name!');
            }

            $this->app = $app;
            $this->logger = $this->app->logger();
            $this->permissions = new Permissions();
        }
    }

    /**
     * Parses a command description.
     *
     * @param string $description
     *
     * @return string
     */
    public function parseDescription($description, $inherit = false)
    {
        $presets = $this->_presets;

        $regex = ($inherit) ? $this->_varRegex['inherit'] : $this->_varRegex['default'];

        $app = $this->app;
        $description = preg_replace_callback($regex, function ($m) use ($presets, $app) {
            foreach ($presets as $preset) {
                if ($m[1] == $preset) {
                    switch ($preset) {
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
                        case 'VERSION':
                        return version();
                        break;
                    }
                }
            }

            if ($m[1] == 'INHERIT') {
                $command = strtolower($m[2]);
                $method = strtolower($m[3]);

                foreach ($app->getCommandList() as $cmd => $info) {
                    if ($command == $cmd) {
                        $class = $info['class'];

                        if (in_array($method, $info['sub_commands']) && $method !== 'index') {
                            $reflection = new ReflectionMethod(get_class($class), $method);
                            $content = $this->getSubCommandDescription($class, $method, $reflection);

                            $content .= ' '.$this->parseDescription("({PREFIX}$cmd $method)");

                            return rtrim($content, '.');
                        }
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
     * @param \Core\Command\Command     $command
     * @param string                    $subCommand
     * @param \ReflectionMethod         $reflection
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
                } elseif (preg_match($this->_varRegex['inherit'], $line)) {
                    $line = $this->parseDescription($line, true);
                    $lines[] = $line;
                }
            }
        }

        if (count($lines) > 0) {
            $desc = implode(' ', $lines);
        }

        return ($desc == '') ? 'No description provided.' : $desc;
    }

    /**
     * Checks if a user has the requested permission.
     *
     * @param string $permission
     *
     * @return bool
     */
    protected function can($permission)
    {
        return $this->permissions->can($permission, $this->author);
    }
}
