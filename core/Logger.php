<?php

namespace Core;

use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    /**
     * Logger instance.
     *
     * @var \Monolog\Logger
     */
    private $_logger;

    /**
     * Ctor.
     *
     * @return void
     */
    public function __construct($loggerName = '')
    {
        if ($loggerName == '') {
            $loggerName = 'NoNamedLog';
        }

        $logNameAndDate = $loggerName.'_'.date('Y-m-d_h-i-s');
        $log = logs_path().'/'.$logNameAndDate.'.log';

        $this->_logger = new MonoLogger($loggerName);

        // This will log default logs from DiscordPHP as well
        $this->_logger->pushHandler(new StreamHandler($log, MonoLogger::DEBUG));
    }

    /**
     * Gets the Monolog logger instance.
     *
     * @return \Monolog\Logger
     */
    public function get()
    {
        return $this->_logger;
    }

    /**
     * Sends a Monolog::INFO message to the logger.
     *
     * @param string $text
     *
     * @return bool
     */
    public function info($text)
    {
        return $this->get()->info($text);
    }

    /**
     * Sends a Monolog::WARNING message to the logger.
     *
     * @param string $text
     *
     * @return bool
     */
    public function warn($text)
    {
        $this->get()->warn($text);
    }

    /**
     * Sends a Monolog::ERROR message to the logger.
     *
     * @param string $text
     *
     * @return bool
     */
    public function error($text)
    {
        $this->get()->error($text);
    }
}
