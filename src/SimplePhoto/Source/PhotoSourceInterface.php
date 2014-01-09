<?php namespace SimplePhoto\Source;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
interface PhotoSourceInterface
{
    /**
     * Process the file input data
     *
     * @param $photoData
     *
     * @return mixed
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
