<?php

namespace Core\Foundation;

use Core\Utils\File;
use Dotenv\Dotenv;

class Environment
{
    /**
     * Dotenv instance.
     *
     * @var \Dotenv\Dotenv
     */
    private $_env = null;

    /**
     * .env filename.
     *
     * @var string
     */
    private $_envFile = '';

    /**
     * Ctor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_envFile = base_path().'/.env';
        if (File::exists($this->_envFile)) {
            $this->_env = new Dotenv(base_path());
            $this->_env->load();
        }
    }

    /**
     * Gets an environment variable.
     *
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    public function get($key, $default = '')
    {
        if (!is_null($this->_env)) {
            $env = ($this->has($key)) ? getenv($key) : var_export($default, true);

            switch ($env) {
                case 'true':
                return true;
                case 'false':
                return false;
                default:
                return $env;
            }
        }

        return var_export($default, true);
    }

    /**
     * Sets an environment variable in .env.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        if (!is_null($this->_env)) {
            return getenv($key) ? true : false;
        }

        return false;
    }
}
