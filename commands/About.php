<?php

namespace Commands;

use Core\Command;
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
    protected $description = '';

    /**
     * Default command method.
     *
     * @return void
     */
    public function index()
    {
        // Default command method
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
    }

    /**
     * Gives the GitHub link to the source code.
     *
     * @return void
     */
    public function source()
    {
        $this->channel->sendMessage('https://github.com/pazuzu156/anubisbot');
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
}
