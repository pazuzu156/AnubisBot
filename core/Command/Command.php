<?php

namespace Core\Command;

use Core\Foundation\Application;
use Core\Wrappers\Parts\Guild;
use Core\Wrappers\Parts\Member;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Embed\Image;
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
     * The command's usage example to serve in the help.
     *
     * @var string
     */
    protected $usage = '';

    /**
     * Whether or not the command should be hidden in the help output.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Command/Sub Command exmaples array.
     *
     * @var array
     */
    protected $examples = [];

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
     * @var \Core\Wrappers\Parts\Guild
     */
    protected $guild;

    /**
     * The command's Author instance.
     *
     * @var \Core\Wrappers\Parts\Member
     */
    protected $author;

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
    private $_presets = ['PREFIX', 'NAME', 'VERSION', 'COMMAND'];

    /**
     * Variable regex array.
     *
     * @var array
     */
    private $_varRegex = [
        'default' => '/\{([a-zA-Z]+)\}/i',
        'inherit' => '/\{(INHERIT)\_([A-Z]+)(\_([A-Z\_]+))?\}/i',
        'example' => '/@example\s\{([a-zA-Z]+)\}/i',
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
        }
    }

    /**
     * Parses a command description.
     *
     * @param string $description
     * @param string $regex
     *
     * @return string
     */
    public function parseDescription($description, $regex = 'default')
    {
        $presets = $this->_presets;

        $regex = $this->_varRegex[$regex];

        $app = $this->app;
        $description = preg_replace_callback($regex, function ($m) use ($presets, $app) {
            foreach ($presets as $preset) {
                if ($m[1] == $preset) {
                    switch ($preset) {
                        case 'PREFIX':
                        return $this->getPrefix();
                        case 'NAME':
                        return env('NAME', '');
                        case 'VERSION':
                        return version();
                        case 'COMMAND':
                        return $this->getPrefix().$this->name;
                    }
                }
            }

            if ($m[1] == 'INHERIT') {
                $command = strtolower($m[2]);

                if (isset($m[3])) {
                    if (str_replace('_', '', $m[3]) == $m[4]) {
                        $method = strtolower($m[4]);
                    }
                } else {
                    $method = 'index';
                }

                foreach ($app->getCommandList() as $cmd => $info) {
                    if ($command == $cmd) {
                        $class = $info['class'];

                        if ($method == 'index') {
                            $content = $class->getDescription().' ({PREFIX}'.$cmd.')';

                            return rtrim($this->parseDescription($content), '.');
                        } else {
                            $reflection = new ReflectionMethod(get_class($class), $method);
                            $content = $this->getSubCommandDescription($class, $method, $reflection);
                            $content .= ' '.$this->parseDescription("({PREFIX}$cmd $method)");

                            return rtrim($content, '.');
                        }
                    }
                }
            }

            return str_replace('@example ', '', $m[0]);
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
     * Gets the command's raw description.
     *
     * @return string|bool
     */
    public function getDescription()
    {
        if (!empty($this->description)) {
            return $this->description;
        }

        return false;
    }

    /**
     * Returns a command exmaple.
     *
     * @param string $key
     *
     * @return string|bool
     */
    public function getExample($key)
    {
        if (isset($this->examples[$key])) {
            return $this->examples[$key];
        }

        return false;
    }

    /**
     * Gets the command's raw usage example.
     *
     * @return string|bool
     */
    public function getUsage()
    {
        if (!empty($this->usage)) {
            return $this->usage;
        }

        return false;
    }

    /**
     * Gets the command's parsed description.
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->app->bot()->getCommand($this->name)->description;
    }

    /**
     * Returns whether or not a command is hidden in the help output.
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
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
        $this->guild = new Guild($this->channel->guild);
        // $this->author = new Member($this->guild, $this->message->author);
        $this->author = $this->message->author;
    }

    /**
     * Extrapolates the sub command description from it's method's docblock.
     *
     * @param \Core\Command\Command $command
     * @param string                $subCommand
     * @param \ReflectionMethod     $reflection
     *
     * @return string
     */
    public function getSubCommandDescription(Command $command, $subCommand, ReflectionMethod $reflection = null)
    {
        if (is_null($reflection)) {
            $reflection = new ReflectionMethod(get_class($command), $subCommand);
        }

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
                    $line = $this->parseDescription($line, 'inherit');
                    $lines[] = $line;
                } elseif (preg_match($this->_varRegex['example'], $line)) {
                    $this->examples[$subCommand] = $this->parseDescription($line, 'example');
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
        // bot owner is always true
        if ($this->isBotOwner() && env('OVERRIDE_PERMISSIONS', false)) {
            return true;
        }

        $can = false;
        foreach ($this->author->roles as $role) {
            if ($role->permissions->{$permission}) {
                $can = true;
            }
        }

        return $can;
    }

    /**
     * Returns whether or not the author is the bot owner.
     *
     * @return bool
     */
    protected function isBotOwner()
    {
        if (env('BOT_OWNER') == $this->author->user->id) {
            return true;
        }

        return false;
    }

    /**
     * Gets the server's bot spam channel (current if one isn't set).
     *
     * @return \Discord\Parts\Channel\Channel
     */
    protected function getBotSpam()
    {
        $isbsset = false;

        if ($this->guild->dataFile()->exists()) {
            $dataFile = $this->guild->dataFile()->getAsArray();

            if (isset($dataFile['bot_spam_channel'])) {
                return $this->guild->channels->get('id', $dataFile['bot_spam_channel']['id']);
            }
        }

        return $this->channel;
    }

    /**
     * Returns a Member part.
     *
     * @param mixed $member
     *
     * @return \Core\Wrappers\Parts\Member
     */
    protected function member($member)
    {
        $id = rtrim(str_replace('<@', '', $member), '>');

        return $this->guild->members->get('id', $id);
    }

    /**
     * Returns a list of banned users.
     *
     * @return array
     */
    protected function getBannedUsers()
    {
        return $this->app->getBannedUsers($this->guild);
    }

    /**
     * Bans a user.
     *
     * @param \Core\Wrappers\Parts\Member|\Discord\Parts\User\Member $member
     * @param int                                                    $count
     *
     * @return \React\Promise\Promise
     */
    protected function banUser($member, $count = 50)
    {
        return $this->app->banUser($member, $count);
    }

    /**
     * Creates a new Discord Embed.
     *
     * @param array $options
     *
     * @return \Discord\Parts\Embed\Embed
     */
    protected function createEmbed(array $options)
    {
        $options['timestamp'] = carbon()->toIso8601String();

        if (!isset($options['type'])) {
            $options['type'] = 'rich';
        }

        // handle colors
        if (isset($options['color'])) {
            if (substr($options['color'], 0, 1) == '#') {
                $options['color'] = hex_to_int($options['color']);
            }
        }

        return $this->app->bot()->factory(Embed::class, $options);
    }

    /**
     * Creates a new Embed Image.
     *
     * @param string $url
     * @param array  $options
     *
     * @return \Discord\Parts\Embed\Image
     */
    protected function embedImage($url, array $options = [])
    {
        $image = $this->app->bot()->factory(Image::class, [
            'url' => $url,
        ]);

        if (!empty($options)) {
            foreach ($options as $key => $value) {
                switch ($key) {
                    case 'proxy_url':
                        $image->proxy_url = $value;
                        break;
                    case 'width':
                        $image->width = $value;
                        break;
                    case 'height':
                        $image->height = $value;
                        break;
                }
            }
        }

        return $image;
    }

    /**
     * Returns the bot's prefix and space if required.
     *
     * @return string
     */
    protected function getPrefix()
    {
        return $this->app->getPrefix();
    }
}
