<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Transformer;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
interface TransformerInterface
{
    /**
     * @param string $file
     * @param array $transformOptions
     * @param array $parameters
     * @return mixed
     */
    public function transform($file, array $transformOptions = array(), array $parameters = array());

    /**
     * @param array $transformOptions
     * @return mixed
     */
    public function generateName(array $transformOptions = array());
}
