<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Toolbox;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class ArrayUtils
{
    /**
     * @param array $array
     *
     * @return mixed
     */
    public static function first(array $array)
    {
        return reset($array);
    }

    /**
     * Ensures that specified keys exists in the array
     *
     * @param array $haystack
     *
     * @return bool
     */
    public static function hasKeys(array $haystack)
    {
        $fails = false;
        $keys = func_get_args();
        array_shift($keys);

        foreach ($keys as $key) {
            if (!array_key_exists($key, $haystack)) {
                $fails = true;
                break;
            }
        }

        return !$fails;
    }
}
