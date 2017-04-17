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

        if (env('LOG_TO_FILE', false)) {
            $this->_logger->pushHandler(new StreamHandler($log, MonoLogger::INFO));
        }
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
        if (env('LOG_BOT', true)) {
            return $this->get()->info($text);
        }

        return false;
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
        if (env('LOG_BOT', true)) {
            return $this->get()->warn($text);
        }

        return false;
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
        if (env('LOG_BOT', true)) {
            return $this->get()->error($text);
        }

        return false;
    }
}
