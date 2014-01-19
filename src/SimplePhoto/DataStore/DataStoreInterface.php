<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\DataStore;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
interface DataStoreInterface
{
    /**
     * @return mixed
     */
    public function getConnection();

    /**
     * @param array $values
     *      - [String] storageName
     *      - [String] filePath
     *
     * @return int Photo ID
     */
    public function addPhoto(array $values);

    /**
     * @param int $photoId
     *
     * @return array Photo Details
     */
    public function getPhoto($photoId);

    /**
     * @param int $photoId
     *
     * @return boolean
     */
    public function deletePhoto($photoId);
}
