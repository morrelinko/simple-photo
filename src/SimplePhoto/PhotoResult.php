<?php namespace SimplePhoto;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
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
     * @var string
     */
    protected $originalFilePath;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $path;

    /**
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
            "photo_id" => null,
            "file_path" => null,
            "storage_name" => null,
            "created_at" => null,
            "updated_at" => null
        ), $photoData);

        $this->setId($photoData["photo_id"]);
        $this->setStorage($photoData["storage_name"]);
        $this->setFilePath($photoData["file_path"]);
        $this->setCreatedAt($photoData["created_at"]);
        $this->setUpdatedAt($photoData["created_at"]);
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
        $this->id = $id;
    }

    /**
     * @param string $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
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