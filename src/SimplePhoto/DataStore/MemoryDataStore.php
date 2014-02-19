<?php

namespace SimplePhoto\DataStore;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class MemoryDataStore implements DataStoreInterface
{
    /**
     * @var array
     */
    protected $store = array();

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->store;
    }

    /**
     * {@inheritDoc}
     */
    public function addPhoto(array $values)
    {
        $values['createdAt'] = date("Y-m-d H:i:s");
        $values['updatedAt'] = $values['createdAt'];

        $id = ++$this->count;
        $this->store[$id] = array(
            'id' => $id,
            'file_name' => $values['fileName'],
            'storage_name' => $values['storageName'],
            'file_extension' => $values['fileExtension'],
            'file_size' => $values['fileSize'],
            'file_path' => $values['filePath'],
            'file_mime' => $values['fileMime'],
            'created_at' => $values['createdAt'],
            'updated_at' => $values['updatedAt']
        );

        return $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getPhoto($photoId)
    {
        if (isset($this->store[$photoId]) && ($photo = $this->store[$photoId])) {
            return $photo;
        }

        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotos(array $photoIds)
    {
        $store = $this->store;
        $photos = array();

        array_map(function ($id) use (&$photos, $store) {
            if (array_key_exists($id, $store)) {
                $photos[] = $store[$id];
            }
        }, $photoIds);

        return $photos;
    }

    /**
     * {@inheritDoc}
     */
    public function deletePhoto($photoId)
    {
        unset($this->store[$photoId]);
    }
}
