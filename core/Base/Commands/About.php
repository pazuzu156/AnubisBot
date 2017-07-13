<?php

namespace Core\Base\Commands;

use Core\Command\Command;
use DateTime;

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
        $name = env('NAME', '');
        $version = version();

        $this->channel->sendMessage("$name is a bot written in PHP. Current version: v$version");
    }

    /**
     * Runs all of the about sub commands.
     *
     * @return void
     */
    public function all()
    {
        $this->index();
        $this->uptime();
        $this->source();
        $this->invite();
        $this->invitebot();
    }

    /**
     * Gives the GitHub link to the source code.
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
     * @return void
     */
    public function invitebot()
    {
        $baseurl = 'https://discordapp.com/oauth2/authorize?scope=bot';
        $url = $baseurl.'&client_id='.$this->_clientId.'&permissions='.$this->_permissions;

        dump($url);
    }
}
