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
     * @return PhotoSourceInterface
     */
    public function process();

    /**
     * Name of file
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the file to be uploaded
     *
     * @return string
     */
    public function getFile();

    /**
     * @return string
     */
    public function getMime();

    /**
     * Checks if the source file data is valid
     *
     * @return boolean
     */
    public function isValid();
}
