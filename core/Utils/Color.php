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
     * Returns an RGB color to an integer color.
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
            | (($green & 0x0ff) << 8)
            | ($blue & 0x0ff);
    }

    /**
     * Converts an RGB color to a hexadecimal color.
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return string
     */
    public static function rgbToHex($red, $green, $blue)
    {
        $red = dechex($red);
        $green = dechex($green);
        $blue = dechex($blue);

        return strtoupper('#'.$red.$green.$blue);
    }

    /**
     * Converts an integer color to an RGB color.
     *
     * @param int $int
     *
     * @return array
     */
    public static function intToRgb($int)
    {
        return [
            'red'   => ($int >> 16) & 255,
            'green' => ($int >> 8) & 255,
            'blue'  => $int & 255,
        ];
    }

    /**
     * Converts integer color to a hexadecimal color.
     *
     * @param int $int
     *
     * @return string
     */
    public static function intToHex($int)
    {
        $rgb = self::intToRgb($int);

        return self::rgbToHex($rgb['red'], $rgb['green'], $rgb['blue']);
    }

    /**
     * Converts hexadecimal color to RGB.
     *
     * @param string $hex
     *
     * @return array
     */
    public static function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) == 3) {
            $hex .= $hex;
        }

        return [
            'red' => hexdec(substr($hex, 0, 2)),
            'green' => hexdec(substr($hex, 2, 2)),
            'blue' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Converts hexadecimal color to an integer color.
     *
     * @param string $hex
     *
     * @return int
     */
    public static function hexToInt($hex)
    {
        $rgb = self::hexToRgb($hex);

        return self::rgbToInt($rgb['red'], $rgb['green'], $rgb['blue']);
    }
}
