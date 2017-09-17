<?php

namespace Core\Utils;

class BotData
{
    /**
     * Gets the value of the given key in the data file.
     *
     * @param string $key
     *
     * @return mixed|bool
     */
    public static function get($key)
    {
        $df = new DataFile(data_path().'/botinfo.json');

        if ($df->exists()) {
            return $df->get($key);
        } else {
            $file = File::open(data_path().'/botinfo.json', 'w');
            $file->write(json_encode([]));
            $file->close();
        }

        return false;
    }

    /**
     * Returns the contents of the data file as an object.
     *
     * @return stdObject
     */
    public static function getAsObject()
    {
        return File::getAsObject(data_path().'/botinfo.json');
    }

    /**
     * Returns the contents of the data file as an array.
     *
     * @return array
     */
    public static function getAsArray()
    {
        return File::getAsArray(data_path().'/botinfo.json');
    }

    /**
     * Writes the given data to the data file.
     *
     * @param array $data
     *
     * @return void
     */
    public static function write($data)
    {
        File::writeAsJson(data_path().'/botinfo.json', $data);
    }
}
