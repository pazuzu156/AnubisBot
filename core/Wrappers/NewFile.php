<?php

namespace Core\Wrappers;

class NewFile
{
    private $_handle;

    private $_filename;

    public function __construct($filename, $mode = 'r')
    {
        $this->_filename = $filename;
        $this->_handle = fopen($filename, $mode);
    }

    public static function open($filename, $mode = 'r')
    {
        return new NewFile($filename, $mode);
    }

    public function read()
    {
        return fread($this->_handle, filesize($this->_filename));
    }

    public function write($content)
    {
        fwrite($this->_handle, $content);

        return $this;
    }

    public function close()
    {
        fclose($this->_handle);
    }

    public static function getAsJson($filename)
    {
        $file = NewFile::open($filename, 'r');
        $content = $file->read();
        $file->close();

        return $content;
    }

    public static function getAsObject($filename, $assoc = false)
    {
        $content = self::getAsJson($filename);

        return json_decode($content, $assoc);
    }

    public static function getAsArray($filename)
    {
        return self::getAsObject($filename, true);
    }

    // seems for some reason the app fails to work right when
    // using JSON_PRETTY_PRINT..... without works fine
    public static function writeAsJson($filename, $content)
    {
        if (is_array($content)) {
            ksort($content);
        }

        $file = NewFile::open($filename, 'w');
        $file->write(json_encode($content));
        $file->close();
    }
}
