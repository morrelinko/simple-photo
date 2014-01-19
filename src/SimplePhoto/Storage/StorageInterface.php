<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Storage;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
interface StorageInterface
{
    /**
     * @param string $source File source
     * @param string $name File name
     * @param array $options
     *
     * @return mixed
     */
    public function upload($source, $name, array $options = array());

    /**
     * Delete photo file
     *
     * @param string $file
     *
     * @return boolean
     */
    public function deletePhoto($file);

    /**
     * @param string $file
     *
     * @return mixed
     */
    public function getPhotoPath($file);

    /**
     * @param string $file
     *
     * @return mixed
     */
    public function getPhotoUrl($file);

    /**
     * @param string $file
     *
     * @return mixed
     */
    public function getPhotoResource($file);

    /**
     * Checks if photo exists
     *
     * @param string $file
     *
     * @return boolean
     */
    public function exists($file);
}
