<?php

namespace Commands;

use Carbon\Carbon;
use Core\Command;

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
     * Gives the GitHub link to the source code.
     *
     * @return void
     */
    public function source()
    {
        $this->channel->sendMessage('https://github.com/pazuzu156/anubisbot');
    }

    public function uptime()
    {
        $start = $GLOBALS['START_TIME'];
        $time = Carbon::createFromTimestamp($start);

        $years = $time->diffInYears();
        $months = $time->diffInMonths();
        $weeks = $time->diffInWeeks();
        $days = $time->diffInDays() % 24;
        $mins = $time->diffInMinutes() % 60;
        $secs = $time->diffInSeconds() % 60;

        $uptime = 'Uptime: ';
        $year = '';
        $month = '';
        $day = '';
        $min = '';
        $sec = '';

        if ($years > 0) {
            if ($years == 1) {
                $year = '1 year ';
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

        $uptime .= $year.$day.$day.$min.$sec;

        $this->channel->sendMessage($uptime);
    }
}
