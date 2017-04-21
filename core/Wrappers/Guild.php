<?php

namespace Core\Wrappers;

class Guild
{
    /**
     * Guild instance.
     *
     * @var \Discord\Parts\Guild\Guild
     */
    private $_guild;

    /**
     * Ctor.
     *
     * @param \Discord\Parts\Channel\Channel
     *
     * @return void
     */
    public function __construct($channel)
    {
        $this->_guild = $channel->guild;
    }

    /**
     * Returns the Guild ID in MD5 format.
     *
     * @return string
     */
    public function getHashedId()
    {
        return md5($this->_guild->id);
    }

    /**
     * Gets the Guild's data file location.
     *
     * @return string
     */
    public function dataFile()
    {
        return data_path().'/'.$this->getHashedId();
    }

    /**
     * Used to dynamically call on Guild class methods.
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array([$this->_guild, $method], $params);
    }

    /**
     * Used to dynamically call on Guild class properties.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->_guild->{$property};
    }
}
