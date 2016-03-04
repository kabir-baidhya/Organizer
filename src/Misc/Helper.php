<?php
/*
 * This file is part of the Organizer package.
 *
 * (c) Kabir Baidhya <kabeer182010@gmail.com>
 *
 */

namespace Gckabir\Organizer\Misc;

class Helper
{
    /**
     * Checks if the a string starts with a given substring.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return substr($haystack, 0, $length) === $needle;
    }

    /**
     * Checks if the a string ends with a given substring.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }

    /**
     * Checks if the string contains wildcard symbols (*, ?, [, ]).
     *
     * @param string $text
     *
     * @return bool
     */
    public static function hasWildcards($text)
    {
        return (bool) preg_match('/(\*|\?|\[|\])/i', $text);
    }
}
