<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class PhotoResult
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $storage;

    /**
     * Original photo path if modified
     *
     * @var string
     */
    protected $originalFilePath;

    /**
     * Photo File Path
     *
     * @var string
     */
    protected $filePath;

    /**
     * Photo File name
     *
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $fileExtension;

    /**
     * @var string
     */
    protected $fileSize;

    /**
     * @var
     */
    protected $mime;

    /**
     * @var string
     */
    protected $originalPath;

    /**
     * Photo Path
     *
     * @var string
     */
    protected $path;

    /**
     * Photo Url
     *
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $updatedAt;

    /**
     * @param array $photoData
     *
     * @return PhotoResult
     */
    public function __construct($photoData)
    {
        $photoData = array_merge(array(
            'id' => null,
            'file_name' => null,
            'file_path' => null,
            'file_extension' => null,
            'file_size' => 0,
            'storage_name' => null,
            'created_at' => null,
            'updated_at' => null
        ), $photoData);

        $this->setId($photoData['id']);
        $this->setFilename($photoData['file_name']);
        $this->setFileExtension($photoData['file_extension']);
        $this->setFileSize($photoData['file_size']);
        $this->setStorage($photoData['storage_name']);
        $this->setFilePath($photoData['file_path']);
        $this->setMime($photoData['file_mime']);
        $this->setCreatedAt($photoData['created_at']);
        $this->setUpdatedAt($photoData['created_at']);
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function fileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function fileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * @return string
     */
    public function fileSize()
    {
        return (int) $this->fileSize;
    }

    /**
     * @return string
     */
    public function storage()
    {
        return $this->storage;
    }

    /**
     * @return string
     */
    public function originalFilePath()
    {
        return $this->originalFilePath;
    }

    /**
     * @return string
     */
    public function filePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function mime()
    {
        return $this->mime;
    }

    /**
     * @return string
     */
    public function originalPath()
    {
        return $this->originalPath;
    }

    /**
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function createdAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function updatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * @param string $fileName
     */
    public function setFilename($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @param string $fileExtension
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    /**
     * @param string $fileSize
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @param string $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param string $mime
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
    }

    /**
     * @param string $path
     */
    public function setOriginalPath($path)
    {
        $this->originalPath = $path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param string $originalFilePath
     */
    public function setOriginalFilePath($originalFilePath)
    {
        $this->originalFilePath = $originalFilePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
