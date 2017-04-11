<?php

namespace Core;

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
        if (file_exists($this->_envFile)) {
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

    /**
     * Sets an environment variable in .env.
     *
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function set($key, $value)
    {
        if (!is_null($this->_env)) {
            $fh = fopen($this->_envFile, 'r');
            $fc = fread($fh, filesize($this->_envFile));
            fclose($fh);

            $lines = explode("\r\n", $fc);
            // make sure lines are exploded, could be \n!
            if (count($lines) == 1) {
                $lines = explode("\n", $fc);
            }
            $write = false;

            for ($i = 0; $i < count($lines); $i++) {
                $line = $lines[$i];
                if (!empty($line)) {
                    $exp = explode('=', $line);

                    if ($exp[0] == $key) {
                        $exp[1] = $value;
                        $line = implode('=', $exp);

                        $lines[$i] = $line;
                        $write = true;
                    }
                }
            }

            if ($write) {
                $fh = fopen($this->_envFile, 'w');
                $nfc = implode(PHP_EOL, $lines);
                fwrite($fh, $nfc);
                fclose($fh);

                return true;
            }
        }

        return false;
    }
}
