<?php

namespace Core\Wrappers\Parts;

class Guild extends Part
{
    /**
     * Returns the Guild ID in MD5 format.
     *
     * @return string
     */
    public function getHashedId()
    {
        return md5($this->part->id);
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
}
