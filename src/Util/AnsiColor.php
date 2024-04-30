<?php
/**
 * 06/2021 created, source: https://gist.github.com/superbrothers/3431198.
 *
 * php-ansi-color
 *
 * Original
 *     https://github.com/loopj/commonjs-ansi-color
 *
 * example usage:
 *      echo AnsiColor::colorText("Success", "green+bold") . " Something was successful!");
 */

namespace Topdata\TopdataQueueHelperSW6\Util;

class AnsiColor
{
    protected static array $ANSI_CODES = [
        'off'        => 0,
        'bold'       => 1,
        'italic'     => 3,
        'underline'  => 4,
        'blink'      => 5,
        'inverse'    => 7,
        'hidden'     => 8,
        // ---------------
        'black'      => 30,
        'red'        => 31,
        'green'      => 32,
        'yellow'     => 33,
        'blue'       => 34,
        'magenta'    => 35,
        'cyan'       => 36,
        'white'      => 37,
        // ---------------
        'black_bg'   => 40,
        'red_bg'     => 41,
        'green_bg'   => 42,
        'yellow_bg'  => 43,
        'blue_bg'    => 44,
        'magenta_bg' => 45,
        'cyan_bg'    => 46,
        'white_bg'   => 47,
    ];

    /**
     * 01/2023 created.
     */
    public static function colorText(string $msg, string $color): string
    {
        $color_attrs = explode('+', $color);
        $ansi_str = '';
        foreach ($color_attrs as $attr) {
            $ansi_str .= "\033[" . self::$ANSI_CODES[$attr] . 'm';
        }
        $ansi_str .= $msg . "\033[" . self::$ANSI_CODES['off'] . 'm';

        return $ansi_str;
    }

//    public static function replace($full_text, $search_regexp, $color)
//    {
//        $new_text = preg_replace_callback(
//            "/($search_regexp)/",
//            function ($matches) use ($color) {
//                return self::set($matches[1], $color);
//            },
//            $full_text
//        );
//        return is_null($new_text) ? $full_text : $new_text;
//    }
}
