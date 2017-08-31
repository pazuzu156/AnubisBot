<?php

namespace Core\Base\Commands\Info;

use Core\Command\Command;
use Core\Utils\Color;
use DateTime;
use GuzzleHttp\Client;

class About extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'about';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Retrieve information about {NAME}';

    /**
     * Pazuzu156 Discord server invite link.
     *
     * @var string
     */
    private $_inviteLink = 'https://discord.gg/Bg3wQs2';

    /**
     * Official AnubisBot Github repo url.
     *
     * @var string
     */
    private $_sourceUrl = 'https://github.com/pazuzu156/anubisbot';

    /**
     * Bot's client id.
     *
     * @var string
     */
    private $_clientId = '327322469197283328';

    /**
     * Bots permissions bitwise value.
     *
     * @var string
     */
    private $_permissions = '268823574';

    /**
     * Default command method.
     *
     * @return void
     */
    public function index()
    {
        $version = version();
        $bot = $this->app->getBotUser();
        $member = $this->member($bot->id);

        $userRolesArr = [];
        foreach ($member->roles as $role) {
            $userRolesArr[] = $role->name;
        }

        $roles = implode(', ', $userRolesArr);
        $roles = rtrim($roles, ', ');

        $fields = [
            [
                'name'  => 'Name',
                'value' => $bot->username.'#'.$bot->discriminator,
            ],
            [
                'name'  => 'ID',
                'value' => $bot->id,
            ],
        ];

        if (!is_null($member->nick)) {
            $fields[] = [
                'name'  => 'Nickname',
                'value' => $member->nick,
            ];
        }

        if (!is_null($member->game->name)) {
            $fields[] = [
                'name'  => 'Current Presence',
                'value' => $member->game->name,
            ];
        }

        if (!is_null($roles) && $roles !== '') {
            $fields[] = [
                'name'  => 'Roles',
                'value' => $roles,
            ];
        }

        $embed = $this->createEmbed([
            'title'       => 'About '.env('NAME', ''),
            'color'       => Color::INFO,
            'thumbnail'   => $this->embedImage($bot->avatar),
            'fields'      => $fields,
            'description' => env('NAME', '').' is a bot written in PHP. Version: v'.$version,
        ]);

        $c = $this->channel;
        $c->sendMessage('', false, $embed)->then(function () use ($c) {
            $prefix = $this->getPrefix().'about ';

            $acmds = ['all', 'uptime', 'source', 'invite', 'invitebot'];
            $cmds = '`';

            $x = 1;
            foreach ($acmds as $cmd) {
                if ($x == count($acmds)) {
                    $cmds .= ' or `'.$prefix.$cmd.'`';
                } else {
                    $cmds .= ' `'.$prefix.$cmd.'`,';
                }

                $x++;
            }

            $msg = 'Want to find out more about '.env('NAME')."?\nType $cmds";
            $c->sendMessage($msg);
        });
    }

    /**
     * Runs all of the about sub commands.
     *
     * @example {COMMAND} all
     *
     * @return void
     */
    public function all()
    {
        // $this->index();
        $this->uptime();
        $this->source();
        $this->invite();
        $this->invitebot();
    }

    /**
     * Gives the GitHub link to the source code.
     *
     * @example {COMMAND} source
     *
     * @return void
     */
    public function source()
    {
        $this->channel->sendMessage($this->_sourceUrl);
    }

    /**
     * Displays the amount of time the bot has been live.
     *
     * @example {COMMAND} uptime
     *
     * @return void
     */
    public function uptime()
    {
        $start = $GLOBALS['START_TIME'];

        $dtF = new DateTime('@'.time());
        $dtT = new DateTime("@$start");
        $diff = $dtF->diff($dtT);

        $years = $diff->format('%y');
        $months = $diff->format('%m');
        $days = $diff->format('%d');
        $hours = $diff->format('%h');
        $mins = $diff->format('%i');
        $secs = $diff->format('%s');

        $year = '';
        $month = '';
        $day = '';
        $hour = '';
        $min = '';
        $sec = '';

        if ($years > 0) {
            if ($years == 1) {
                $year = '1 year, ';
            } else {
                $year = $years.' years ';
            }
        }
        if ($months > 0) {
            if ($months == 1) {
                $month = '1 month ';
            } else {
                $month = $months.' months ';
            }
        }
        if ($days > 0) {
            if ($days == 1) {
                $day = '1 day ';
            } else {
                $day = $days.' days ';
            }
        }
        if ($hours > 0) {
            if ($hours == 1) {
                $hour = '1 hour ';
            } else {
                $hour = $hours.' hours ';
            }
        }
        if ($mins > 0) {
            if ($mins == 1) {
                $min = '1 minute ';
            } else {
                $min = $mins.' minutes ';
            }
        }
        if ($secs > 0) {
            if ($secs == 1) {
                $sec = '1 second';
            } else {
                $sec = $secs.' seconds';
            }
        }

        $uptime = 'Uptime: '.$year.$month.$day.$hour.$min.$sec;

        $this->channel->sendMessage($uptime);
    }

    /**
     * Gives the official server's invite link.
     *
     * @example {COMMAND} invite
     *
     * @return void
     */
    public function invite()
    {
        $message = 'If you\'re on my server, you can invite using the invite link. If you\'re not on my server, you\'re welcome to join by also using the invite link.';

        $this->channel->sendMessage($message."\n".$this->_inviteLink);
    }

    /**
     * Brings up an invite link for {NAME}.
     *
     * @example {COMMAND} invitebot
     *
     * @return void
     */
    public function invitebot()
    {
        $baseurl = 'https://discordapp.com/oauth2/authorize?scope=bot';
        $url = $baseurl.'&client_id='.$this->_clientId.'&permissions='.$this->_permissions;

        $this->channel->sendMessage("You can use this url: $url to invite the bot to your server");
    }

    /**
     * Gets the latest changelog.
     *
     * @example {COMMAND} changes
     *
     * @return void
     */
    public function changes()
    {
        $client = new Client();
        $response = $client->get('https://api.kalebklein.com/anubisbot/changes');

        $content = $response->getBody()->getContents();

        $this->channel->sendMessage($content);
    }
}
