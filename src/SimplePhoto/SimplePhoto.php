<?php namespace SimplePhoto;

use SimplePhoto\DataStore\DataStoreInterface;
use SimplePhoto\Source\FilePathUploadSource;
use SimplePhoto\Source\PhotoSourceInterface;
use SimplePhoto\Source\PhpFileUploadSource;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class SimplePhoto
{
    /**
     * @var StorageManager
     */
    protected $storageManager;

    /**
     * @var DataStoreInterface
     */
    protected $dataStore;

    /**
     * Constructor
     *
     * @param StorageManager $storageManager
     * @param DataStoreInterface $dataStore
     */
    public function __construct(StorageManager $storageManager = null, DataStoreInterface $dataStore = null)
    {
        if ($storageManager != null) {
            $this->setStorageManager($storageManager);
        }

        if ($dataStore != null) {
            $this->setDataStore($dataStore);
        }
    }

    /**
     * @return StorageManager
     */
    public function getStorageManager()
    {
        return $this->storageManager;
    }

    /**
     * @param StorageManager $storageManager
     */
    public function setStorageManager(StorageManager $storageManager)
    {
        $this->storageManager = $storageManager;
    }

    /**
     * @return DataStoreInterface
     */
    public function getDataStore()
    {
        return $this->dataStore;
    }

    /**
     * @param DataStoreInterface $dataStore
     */
    public function setDataStore(DataStoreInterface $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    /**
     * @param mixed $photoData
     * @param array $options
     *
     * @return int
     */
    public function uploadFromPhpFileUpload($photoData, array $options = array())
    {
        return $this->uploadFrom($photoData, $options, new PhpFileUploadSource());
    }

    /**
     * @param mixed $photoData
     * @param array $options
     *
     * @return int
     */
    public function uploadFromFilePath($photoData, array $options = array())
    {
        return $this->uploadFrom($photoData, $options, new FilePathUploadSource());
    }

    /**
     * @param mixed $photoData
     * @param array $options
     *      - [Array] transform: enables you to transform photo before saving
     *      - [String] storage: sets the storage system to save photo
     * @param PhotoSourceInterface $photoSource
     *
     * @return int Photo ID
     */
    public function uploadFrom($photoData, array $options = array(), PhotoSourceInterface $photoSource)
    {
        /**
         * @var array $transform
         * @var string $storageName
         */
        extract(array_merge(array(
            "transform" => array(),
            "storageName" => $this->storageManager->getDefault()
        ), $options));

        $photoSource->process($photoData);
        $saveName = $this->generateOriginalSaveName($photoSource->getName());
        $storage = $this->getStorageManager()->get($storageName);

        if ($transform) {
            $uploadPath = null;
        } else {
            $uploadPath = $storage->upload($photoSource->getFile(), $saveName, $options);
        }

        if ($uploadPath && $this->dataStore != null) {
            // Persist uploaded photo data
            return $this->dataStore->addPhoto(array(
                "storageName" => $storageName,
                "filePath" => $uploadPath
            ));
        }

        return false;
    }

    /**
     * @param int $photoId PhotoID
     * @param array $options
     *      - [Array] transform: Customizations to be applied to photo
     *
     * @return array
     */
    public function getPhoto($photoId, array $options = array())
    {
        $photo = $this->dataStore->getPhoto($photoId);

        if ($photo) {
            $fs = $this->storageManager->get($photo["storage_name"]);
            $photo["photo_path"] = $fs->getPhotoPath($photo["file_path"]);
            $photo["photo_url"] = $fs->getPhotoUrl($photo["file_path"]);
        }

        return $photo;
    }

    public function deletePhoto($photoId)
    {
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function generateOriginalSaveName($file)
    {
        $fileName = uniqid(time() . substr(str_shuffle("abcdefABCDEF012345"), 0, 8));
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $savePath = sprintf("%s/%s/%s.%s", date("Y"), date("m"), $fileName, $extension);

        return $savePath;
    }

    private function generateModifiedSaveName($fileName)
    {
    }
}