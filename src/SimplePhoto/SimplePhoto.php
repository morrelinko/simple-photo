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
     * @var array
     */
    protected $options = array();

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
     * @param array $options
     */
    public function __construct(StorageManager $storageManager = null, DataStoreInterface $dataStore = null, $options = array())
    {
        $this->options = array_merge(array(
            "defaults_root" => null,
            "defaults_path" => null
        ), $options);

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
        $options = array_merge(array(
            "transform" => array(),
            "default" => null,
        ), $options);

        $photo = $this->dataStore->getPhoto($photoId);

        if (empty($photo)) {
            // Could not find photo data
            if ($options["default"] == null || !$this->storageManager->has(StorageManager::FALLBACK_STORAGE)) {
                // If default is not set, then no default photo is available
                return array();
            }

            // Construct default data
            $photo = array(
                "photo_id" => 0,
                "storage_name" => StorageManager::FALLBACK_STORAGE,
                "file_path" => $options["default"],
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            );
        }

        $fs = $this->storageManager->get($photo["storage_name"]);
        $photo["photo_path"] = $fs->getPhotoPath($photo["file_path"]);
        $photo["photo_url"] = $fs->getPhotoUrl($photo["file_path"]);

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