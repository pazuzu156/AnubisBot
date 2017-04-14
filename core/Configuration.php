<?php

namespace Core;

class Configuration
{
    /**
     * Gets a config item.
     *
     * @param string $item
     *
     * @return mixed
     */
    public function get($item)
    {
        $item = str_replace('.', '/', $item);
        $items = explode('/', $item);

        $config = require base_path().'/config.php';

        switch (count($items)) {
            case 1:
            return $config[$item];
            case 2:
            return $config[$items[0]][$items[1]];
            case 3:
            return $config[$items[0]][$items[1]][$items[2]];
            case 4:
            return $config[$items[0]][$items[1]][$items[2]][$items[4]];
            default:
            return false;
        }
    }
}
