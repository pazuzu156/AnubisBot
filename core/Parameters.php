<?php

namespace Core;

class Parameters
{
    /**
     * Parameters array.
     *
     * @var array
     */
    private $_params = [];

    /**
     * The command method to call.
     *
     * @var string
     */
    private $_method = 'index';

    /**
     * Ctor.
     *
     * @return void
     */
    public function __construct(array $options = [])
    {
        if (isset($options['params'])) {
            $this->_params = $options['params'];
        }

        if (isset($options['method'])) {
            $this->_method = $options['method'];
        }
    }

    /**
     * Gets the method to call.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Retuns the parameter count.
     *
     * @return int
     */
    public function count()
    {
        return count($this->_params);
    }

    /**
     * Gets a parameter by the index
     *
     * @param int $index
     *
     * @return mixed
     */
    public function get($index)
    {
        if ($this->count()) {
            if ($this->has($index)) {
                return $this->_params[$index];
            }
        }
    }

    /**
     * Checks to see if a parameter exists at the index.
     *
     * @param int $index
     *
     * @return boolean
     */
    public function has($index)
    {
        return isset($this->_params[$index]);
    }

    /**
     * Gets the first parameter.
     *
     * @return mixed
     */
    public function first()
    {
        if($this->has(0)) {
            return $this->get(0);
        }
    }

    /**
     * Gets all parameters.
     *
     * @return array
     */
    public function all()
    {
        return $this->_params;
    }
}
