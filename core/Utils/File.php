<?php

namespace Core\Utils;

class File
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

        if (!file_exists($this->_filename)) {
            touch($filename);
            chmod($filename, 0755);
        }
        
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
    public static function get($filename)
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
        $content = self::get($filename);

        return json_decode($content, $assoc);
    }

    /**
     * Gets JSON formatted test as an array.
     *
     * @param string $filename
     *
     * @return array
     */
    public static function getAsArray($filename, $json = true)
    {
        if ($json) {
            return self::getAsObject($filename, true);
        }

        $content = self::get($filename);
        $exp = explode("\r\n", $content);
        if (count($exp) == 0 || count($exp) == 1) {
            $exp = explode("\n", $content);
        }

        // Remove empty new lines
        foreach ($exp as $key => $value) {
            if ($value == '') {
                unset($exp[$key]);
            }
        }

        return $exp;
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

        self::writeTo($filename, json_encode($content));
    }

    /**
     * Statically write content to a file.
     *
     * @param string $filename
     * @param string $content
     *
     * @return void
     */
    public static function writeTo($filename, $content)
    {
        $file = self::open($filename, 'w');
        $file->write($content);
        $file->close();
    }

    public static function copy($source, $destination)
    {
        return copy($source, $destination);
    }

    public static function move($source, $destination)
    {
        return rename($source, $destination);
    }

    /**
     * Check if a file exists.
     *
     * @param string $filename
     *
     * @return bool
     */
    public static function exists($filename)
    {
        return file_exists($filename);
    }

    /**
     * Deletes a file.
     *
     * @param string $filename
     * @param bool   $tree
     *
     * @return bool
     */
    public static function delete($filename, $tree = false)
    {
        if ($tree) {
            $remove = unlink($filename);
            $name = basename($filename);
            self::deleteTree(str_replace($name, '', $filename));

            return $remove;
        }

        return unlink($filename);
    }

    /**
     * Deletes a directory tree assuming it's empty.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function deleteTree($path)
    {
        $path = rtrim($path, '/');
        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);

            if (count($files) == 0) {
                $return = rmdir($path);
                $parent = substr($path, 0, strripos($path, '/'));
                self::deleteTree($path);

                return $return;
            }
        }

        return false;
    }
}
