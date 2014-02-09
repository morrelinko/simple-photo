<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Source;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
interface PhotoSourceInterface
{
    /**
     * Process the file input data
     *
     * @param $photoData
     *
     * @return PhotoSourceInterface
     */
    public function process($photoData);

    /**
     * Name of file
     *
     * @return mixed
     */
    public function getName();

    /**
     * Gets the file to be uploaded
     *
     * @return mixed
     */
    public function getFile();
}
