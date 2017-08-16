<?php

namespace Core\Utils;

class DataFile
{
    /**
     * DataFile filename.
     *
     * @var string
     */
    private $_dataFile;

    /**
     * Ctor.
     *
     * @return void
     */
    public function __construct($dataFile)
    {
        $this->_dataFile = $dataFile;
    }

    /**
     * Checks if the data file exists.
     *
     * @return bool
     */
    public function exists()
    {
        return File::exists($this->_dataFile);
    }

    /**
     * Checks if the data file has a given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->getAsArray()[$key]);
    }

    /**
     * Gets the value of the given key in the data file.
     *
     * @param string $key
     *
     * @return mixed|bool
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->getAsArray()[$key];
        }

        return false;
    }

    /**
     * Returns the contents of the data file as an object.
     *
     * @return stdObject
     */
    public function getAsObject()
    {
        return File::getAsObject($this->_dataFile);
    }

    /**
     * Returns the contents of the data file as an array.
     *
     * @return array
     */
    public function getAsArray()
    {
        return File::getAsArray($this->_dataFile);
    }

    /**
     * Writes the given data to the data file.
     *
     * @param array $data
     *
     * @return void
     */
    public function write($data)
    {
        File::writeAsJson($this->_dataFile, $data);
    }
}
