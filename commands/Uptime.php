<?php

namespace Commands;

use Carbon\Carbon;
use Core\Command;

class Uptime extends Command
{
    /**
     * The name of your command to register with Discord.
     *
     * @var string
     */
    protected $name = 'uptime';

    /**
     * The command's description to serve in the help.
     *
     * @var string
     */
    protected $description = 'Displays the uptime of the bot';

    /**
     * Displays the uptime (crude, but works).
     *
     * @return void
     */
    public function index()
    {
        var_dump($this->author);
        return;
        $start = $GLOBALS['START_TIME'];
        $time = Carbon::createFromTimestamp($start);

        $years  = $time->diffInYears();
        $months = $time->diffInMonths();
        $weeks  = $time->diffInWeeks();
        $days   = $time->diffInDays();
        $mins   = $time->diffInMinutes();
        $secs   = $time->diffInSeconds();

        $uptime = 'Uptime: ';
        $year   = '';
        $month  = '';
        $day    = '';
        $min    = '';
        $sec    = '';

        if ($years > 0) {
            if ($years == 1) {
                $year = '1 year';
            } else {
                $year = $years.' years';
            }
        }

        if ($months > 0) {
            if ($months == 1) {
                $month = '1 month';
            } else {
                $month = $months.' months';
            }
        }

        if ($days > 0) {
            if ($days == 1) {
                $day = '1 day';
            } else {
                $day = $days.' days';
            }
        }

        if ($mins > 0) {
            if ($mins == 1) {
                $min = '1 minute';
            } else {
                $min = $mins.' minutes';
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
