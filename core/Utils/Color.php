<?php

namespace Core\Utils;

/**
 * Defines Discord colors.
 */
class Color
{
    /**
     * Base Gray Color.
     *
     * @var int
     */
    const BASE = 9079434;

    /**
     * Primary color: lightish-blue.
     *
     * @var int
     */
    const PRIMARY = 680191;

    /**
     * Success color: green.
     *
     * @var int
     */
    const SUCCESS = 2469401;

    /**
     * Warning color: orange.
     *
     * @var int
     */
    const WARNING = 16741135;

    /**
     * Danger color: red.
     *
     * @var int
     */
    const DANGER = 16726303;

    /**
     * Info color: darkish-blue.
     *
     * @var int
     */
    const INFO = 2839703;

    /**
     * Returns an RGB value in integer format.
     *
     * @param int $red
     * @param int $green
     * @param int blue
     *
     * @return int
     */
    public static function rgbToInt($red, $green, $blue)
    {
        return (($red & 0x0ff) << 16)
            | (( $green & 0x0ff) << 8)
            | ($blue & 0x0ff);
    }

    /**
     * Returns an array of RGB values from an integer formated color.
     *
     * @param int $int
     *
     * @return int
     */
    public static function intToRgb($int)
    {
        return [
            'red'   => ($int >> 16) & 255,
            'green' => ($int >> 8) & 255,
            'blue'  => $int & 255,
        ];
    }
}
