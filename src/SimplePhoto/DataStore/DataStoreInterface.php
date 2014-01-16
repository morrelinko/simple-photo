<?php namespace SimplePhoto\DataStore;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
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