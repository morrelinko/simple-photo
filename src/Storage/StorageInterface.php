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
     * @param string $file Source file
     * @param string $name File name
     * @param array $options
     * @return mixed
     */
    public function upload($file, $name, array $options = array());

    /**
     * Gets information about a photo file
     *
     * @param string $file
     * @return false|array false if file does not exists
     */
    public function getInfo($file);

    /**
     * Delete photo file
     *
     * @param string $file
     * @return boolean
     */
    public function deletePhoto($file);

    /**
     * @param string $file
     * @return mixed
     */
    public function getPhotoPath($file);

    /**
     * @param string $file
     * @return mixed
     */
    public function getPhotoUrl($file);

    /**
     * @param string $file
     * @param string $tmpFile temp file photo is saved locally during manipulation
     * @return mixed
     */
    public function getPhotoResource($file, $tmpFile);

    /**
     * Checks if photo exists
     *
     * @param string $file
     * @return boolean
     */
    public function exists($file);
}
