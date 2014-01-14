<?php namespace SimplePhoto\Storage;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
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
