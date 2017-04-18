<?php

namespace Core\Wrappers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class FileSystemWrapper
{
    /**
     * Read file contents as an array.
     *
     * @param string $path
     * @param string $delem
     * @param bool   $lock
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return array
     */
    public static function getAsArray($path, $delim = '', $lock = false)
    {
        if (!Filesystem::exists($path)) {
            throw new FileNotFoundException("Error: the file $path does not exists!");
        }

        $content = self::get($path, $lock);

        $exp = explode("\r\n", $content);
        if (count($exp) == 0) {
            $exp = explode("\n", $content);
        }

        if ($delim !== '') {
            $lines = [];
            foreach ($exp as $line) {
                $e = explode($delim, $line);
                if (count($e) == 2) {
                    $lines[$e[0]] = $e[1];
                }
            }

            $exp = $lines;
        }

        return $exp;
    }

    /**
     * Read file contents in JSON format.
     *
     * @param string $path
     * @param bool   $lock
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return string
     */
    public function getAsJson($path, $lock = false)
    {
        return json_encode(self::getAsArray($path, $lock));
    }

    /**
     * Writes to the file (Alias for Filesystem::put).
     *
     * @param string       $path
     * @param string|array $contents
     * @param bool         $lock
     *
     * @return int
     */
    public static function write($path, $contents, $lock = false)
    {
        // If array, build into string with each item as a line
        if (is_array($contents)) {
            $contents = implode("\n", $contents);
        }

        return Filesystem::put($path, $contents, $lock);
    }

    /**
     * Statically call Illuminate's FileSystem functions dynamically.
     *
     * @param string $function
     * @param array  $params
     *
     * @return mixed
     */
    public static function __callStatic($function, $params)
    {
        return call_user_func_array([new Filesystem(), $function], $params);
    }
}
