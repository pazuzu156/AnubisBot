<?php

namespace Core\Utils;

class DataFile
{
    private $_dataFile;

    public function __construct($dataFile)
    {
        $this->_dataFile = $dataFile;
    }

    public function exists()
    {
        return File::exists($this->_dataFile);
    }

    public function has($key)
    {
        return isset($this->getAsArray()[$key]);
    }

    public function get($key)
    {
        if ($this->has($key)) {
            return $this->getAsArray()[$key];
        }

        return false;
        // return $this->getAsArray()[$key];
        // return File::get($this->_dataFile);
    }

    public function getAsObject()
    {
        return File::getAsObject($this->_dataFile);
    }

    public function getAsArray()
    {
        return File::getAsArray($this->_dataFile);
    }

    public function write($data)
    {
        return File::writeAsJson($this->_dataFile, $data);
    }
}
