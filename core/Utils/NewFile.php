<?php

namespace Core\Utils;

class NewFile
{
    /**
     * File handle instance.
     *
     * @var resource
     */
    private $_handle;

    /**
     * Name of the file being used.
     *
     * @var string
     */
    private $_filename;

    /**
     * Ctor.
     *
     * @return void
     */
    public function __construct($filename, $mode = 'r')
    {
        $this->_filename = $filename;
        $this->_handle = fopen($filename, $mode);
    }

    /**
     * Open a file statically for quickly opening a file.
     *
     * @param string $filename
     * @param string $mode
     *
     * @return \Core\Utils\NewFile
     */
    public static function open($filename, $mode = 'r')
    {
        return new self($filename, $mode);
    }

    /**
     * Reads a file and returns it's contents.
     *
     * @return string
     */
    public function read()
    {
        return fread($this->_handle, filesize($this->_filename));
    }

    /**
     * Writes contents to a file.
     *
     * @param mixed $content
     *
     * @return \Core\Utils\NewFile
     */
    public function write($content)
    {
        fwrite($this->_handle, $content);

        return $this;
    }

    /**
     * Closes the file.
     *
     * @return void
     */
    public function close()
    {
        fclose($this->_handle);
    }

    /**
     * Statically get content in JSON format.
     * (Assumes the file is already in JSON format).
     *
     * @param string $filename
     *
     * @return string
     */
    public static function getAsJson($filename)
    {
        $file = self::open($filename, 'r');
        $content = $file->read();
        $file->close();

        return $content;
    }

    /**
     * Returns JSON formatted text as an object.
     *
     * @param string $filename
     * @param bool   $assoc
     *
     * @return \stdObject
     */
    public static function getAsObject($filename, $assoc = false)
    {
        $content = self::getAsJson($filename);

        return json_decode($content, $assoc);
    }

    /**
     * Gets JSON formatted test as an array.
     *
     * @param string $filename
     *
     * @return array
     */
    public static function getAsArray($filename)
    {
        return self::getAsObject($filename, true);
    }

    /**
     * Write content to a file as JSON.
     *
     * @param string $filename
     * @param mixed  $content
     *
     * @return void
     */
    public static function writeAsJson($filename, $content)
    {
        if (is_array($content)) {
            ksort($content);
        }

        $file = self::open($filename, 'w');
        $file->write(json_encode($content));
        $file->close();
    }
}
