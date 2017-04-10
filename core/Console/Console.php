<?php

namespace Core\Console;

class Console
{
    /**
     * Register console command class.
     *
     * @param mixed $command
     *
     * @return mixed
     */
    public function register($command)
    {
        return new $command();
    }
}
