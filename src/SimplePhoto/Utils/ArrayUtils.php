<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Utils;

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
}
