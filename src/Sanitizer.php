<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\Fixer;

/**
 * This is the sanitizer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Sanitizer
{
    /**
     * Sanitize the message for storage.
     *
     * We're removing all references to the location of the project on the
     * disk here, removing any double spaces for readability, and ensuing the
     * message ends in a full stop.
     *
     * @param string $message
     * @param string $path
     *
     * @return string
     */
    public static function sanitize($message, $path)
    {
        $message = static::sanitizePaths($message, $path);

        $message = str_replace('  ', ' ', $message);
        $message = trim(rtrim($message, '.!?'));

        return "{$message}.";
    }

    /**
     * Sanitize the paths in the message.
     *
     * @param string $message
     * @param string $path
     *
     * @return string
     */
    protected static function sanitizePaths($message, $path)
    {
        $real = realpath($path);

        $message = str_replace("{$real}/", '', $message);
        $message = str_replace($real, '', $message);
        $message = str_replace("{$path}/", '', $message);
        $message = str_replace($path, '', $message);

        if (substr_count($path, '/') > 2) {
            return static::sanitizePaths($message, substr($path, 0, strrpos($path, '/')));
        }

        return $message;
    }
}
