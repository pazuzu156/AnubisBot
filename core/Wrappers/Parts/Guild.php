<?php

namespace Core\Wrappers\Parts;

use Core\Utils\DataFile;

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
     * @return \Core\Utils\DataFile
     */
    public function dataFile()
    {
        return new DataFile(data_path().'/'.$this->getHashedId());
    }
}
