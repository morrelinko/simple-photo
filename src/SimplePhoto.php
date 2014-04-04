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

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use SimplePhoto\DataStore\DataStoreInterface;
use SimplePhoto\Source\FilePathSource;
use SimplePhoto\Source\PhotoSourceInterface;
use SimplePhoto\Source\PhpFileUploadSource;
use SimplePhoto\Storage\StorageInterface;
use SimplePhoto\Toolbox\PhotoCollection;
use SimplePhoto\Utils\FileUtils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
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
     */
    public function __construct(
        StorageManager $storageManager = null,
        DataStoreInterface $dataStore = null
    ) {
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
     * @param mixed $uploadData
     * @param array $options
     *
     * @see SimplePhoto::uploadFrom()
     * @return int
     */
    public function uploadFromPhpFileUpload($uploadData, array $options = array())
    {
        return $this->upload(new PhpFileUploadSource($uploadData), $options);
    }

    /**
     * @param mixed $file
     * @param array $options
     *
     * @see SimplePhoto::uploadFrom()
     * @return int
     */
    public function uploadFromFilePath($file, array $options = array())
    {
        return $this->upload(new FilePathSource($file), $options);
    }

    /**
     * Upload Photo
     *
     * @param PhotoSourceInterface $photoSource
     * @param array $options Options available
     * <pre>
     * transform: options for transforming photo before saving
     * storage: storage system to save photo
     * </pre>
     *
     * @return int|bool Photo ID if successful or false otherwise
     */
    public function upload(PhotoSourceInterface $photoSource, array $options = array())
    {
        if ($photoSource->isValid() == false) {
            // No need to go further if source is invalid
            return false;
        }

        /**
         * @var array $transform
         * @var string $storageName
         */
        extract(array_merge(array(
            'transform' => array(),
            'storageName' => $this->storageManager->getDefault()
        ), $options));

        $saveName = $this->generateOriginalSaveName($photoSource->getName());
        $storage = $this->getStorageManager()->get($storageName);
        $fileMime = $photoSource->getMime();

        if ($transform) {
            // If we are to perform photo transformation during upload,
            // transformation specs are applied and the new photo is saved
            // as the original image
            list($uploadPath, $fileSize) = $this->transformPhoto(
                $storage,
                FileUtils::createTempFile($photoSource->getFile()),
                $saveName,
                $fileMime,
                $transform
            );
        } else {
            // Just upload as is
            $fileSize = filesize($photoSource->getFile());
            $uploadPath = $storage->upload(
                $photoSource->getFile(),
                $saveName,
                $options
            );
        }

        if ($uploadPath && $this->dataStore != null) {
            // Persist uploaded photo data
            return (int) $this->dataStore->addPhoto(array(
                'storageName' => $storageName,
                'filePath' => $uploadPath,
                'fileSize' => $fileSize,
                'fileName' => $photoSource->getName(),
                'fileExtension' => FileUtils::getExtension($photoSource->getName()),
                'fileMime' => $fileMime,
            ));
        }

        return false;
    }

    /**
     * Legacy Upload Photo
     *
     * @param mixed $photoData
     * @param array $options Options available
     * <pre>
     * transform: options for transforming photo before saving
     * storage: storage system to save photo
     * </pre>
     * @param PhotoSourceInterface $photoSource
     *
     * @return int Photo ID
     * @deprecated
     */
    public function uploadFrom(
        $photoData,
        PhotoSourceInterface $photoSource,
        array $options = array()
    ) {
        return $this->upload($photoSource->process($photoData), $options);
    }

    /**
     * @param int $photoId PhotoID
     * @param array $options
     *
     * @see SimplePhoto::build()
     * @return PhotoResult|false Returns false if photo is not
     * found and no fallback photo setup & defined
     */
    public function get($photoId, array $options = array())
    {
        $photo = $this->dataStore->getPhoto($photoId);

        return $this->build($photo, $options);
    }

    /**
     * Gets multiple photos
     *
     * @param array $ids List of photo ids
     * @param array $options
     *
     * @see SimplePhoto::build()
     * @return mixed|PhotoCollection
     */
    public function collection(array $ids, array $options = array())
    {
        $photos = $this->dataStore->getPhotos($ids);

        if (empty($photos) && !$this->storageManager->hasFallback()) {
            // If no fallback has been defined, and no photo was found
            // lets just skip the computation that follows.
            return $this->createPhotoCollection();
        }

        $found = array();
        array_map(function ($photo) use ($ids, &$found) {
            // This will be used to build found Photos
            return $found[$photo['photo_id']] = $photo;
        }, $photos);

        $sorted = array();
        foreach ($ids as $index => $id) {
            $photo = array();
            if (array_key_exists($id, $found)) {
                $photo = $found[$id];
            }

            $sorted[$index] = $photo;
        }

        $photos = $this->createPhotoCollection($sorted);
        $simplePhoto = $this; // php 5.3 compatibility
        $photos->transform(function ($photo) use ($simplePhoto, $options) {
            return $simplePhoto->build($photo, $options);
        })->ksort();

        return $photos;
    }

    /**
     * Delete photo
     *
     * @param int $photoId
     *
     * @return bool
     */
    public function delete($photoId)
    {
        $photo = $this->dataStore->getPhoto($photoId);

        if (!$photo) {
            // Photo does not exists, lets assume its deleted
            return true;
        }

        $storage = $this->storageManager->get($photo['storage_name']);

        if ($this->dataStore->deletePhoto($photoId)) {
            if ($storage->deletePhoto($photo['file_path'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Push `PhotoResult` item to an array/iterator
     *
     * @param array|\Iterator $haystack List of items
     * @param array $keys Key names containing photo ID
     * @param callable $callback (Optional) Callback to use for building
     * items to push into the array
     * @param array $options Photo options
     *
     * @see build()
     * @throws \InvalidArgumentException
     */
    public function push(&$haystack, array $keys = array(), \Closure $callback = null, array $options = array())
    {
        if ($haystack instanceof \Iterator) {
            $haystack = iterator_to_array($haystack, true);
        }

        if (!is_array($haystack)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to %s must be an array
                 or implement interface \Iterator',
                __METHOD__
            ));
        }

        // Generate an array of index that will be pushed to the original array
        // if no key is set, by convention, we look for `photo_id`
        $keys = empty($keys) ? array('photo_id' => 'photo') : $keys;
        foreach ($keys as $index => $name) {
            if (is_int($index)) {
                unset($keys[$index]);
                $keys[$name] = substr($name, 0, -3); // Remove '_id'
            }
        }

        if ($callback == null) {
            $callback = function (&$item, $photo, $index, $name) use ($keys) {
                $item[$name] = $photo;

                return $item;
            };
        }

        if (array_values($haystack) === $haystack) {
            // This array is a list
            foreach ($keys as $index => $name) {
                // Get list of photo ids
                $ids = $this->arrayColumn($haystack, $index);
                $photos = $this->collection($ids, $options);
                foreach ($haystack as $key => $item) {
                    $callback($haystack[$key], $photos->get($key), $index, $name);
                }
            }
        } else {
            foreach ($keys as $index => $name) {
                $photo = $this->get($haystack[$index], $options);
                $callback($haystack, $photo, $index, $name);
            }
        }
    }

    /**
     * @param array $photo
     * @param array $photo Photo data
     * @param array $options Available options
     * <pre>
     * fallback: A fallback photo to use when photo is not found
     * transform: Transformation options to be applied to photo
     * </pre>
     *
     * @return bool|PhotoResult
     */
    public function build(array $photo, array $options = array())
    {
        $options = array_merge(array(
            'transform' => array(),
            'fallback' => null,
        ), $options);

        if (empty($photo)) {
            // Could not find photo data
            if ($options['fallback'] == null ||
                !$this->storageManager->has(StorageManager::FALLBACK_STORAGE)
            ) {
                // If default is not set, then no default photo is available
                return false;
            }

            // Construct default data
            $photo = array(
                'photo_id' => 0,
                'storage_name' => StorageManager::FALLBACK_STORAGE,
                'file_name' => pathinfo($options['fallback'], PATHINFO_FILENAME),
                'file_path' => $options['fallback'],
                'file_mime' => 'image/png', // Look into this
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
        }

        $photoResult = new PhotoResult($photo);
        $storage = $this->storageManager->get($photo['storage_name']);

        if (!empty($options['transform'])) {
            // Transformation options available
            $modifiedFileName = $this->generateModifiedSaveName(
                $photo['file_path'],
                $options['transform']
            );

            $photoResult->setOriginalFilePath($photo['file_path']);
            $photoResult->setOriginalPath($storage->getPhotoPath($photo['file_path']));

            if (!$storage->exists($modifiedFileName)) {
                // Only do image manipulation once
                // (ie if file does not exists)
                list($modifiedFileName, $fileSize) = $this->transformPhoto(
                    $storage,
                    $storage->getPhotoResource($photoResult->originalFilePath()),
                    $modifiedFileName,
                    $photo['file_mime'],
                    $options['transform']
                );

                $photoResult->setFileSize($fileSize);
                // Set the file path to the new modified photo path
                $photoResult->setFilePath($modifiedFileName);
            }
        }

        $photoResult->setPath($storage->getPhotoPath($photoResult->filePath()));
        $photoResult->setUrl($storage->getPhotoUrl($photoResult->filePath()));

        return $photoResult;
    }

    /**
     * @param StorageInterface $storage
     * @param string $tmpFile
     * @param string $modifiedFile
     * @param string $mimeType
     * @param array $transform
     *
     * @return string|bool Modified file if successful or false otherwise
     */
    private function transformPhoto(
        StorageInterface $storage,
        $tmpFile,
        $modifiedFile,
        $mimeType,
        array $transform = array()
    ) {
        // Load image for manipulation
        $imagine = new Imagine();
        $transformer = $imagine->open($tmpFile);

        // Start transforming
        if (isset($transform['size'])) {
            list($width, $height) = $transform['size'];
            $transformer->resize(new Box($width, $height));
        }

        if (isset($transform['rotate'])) {
            list($angle, $background) = array_pad($transform['rotate'], 2, null);
            $transformer->rotate((int) $angle, $background);
        }

        $transformer->save($tmpFile, array(
            'format' => FileUtils::getExtensionFromMime($mimeType)
        ));

        clearstatcache();

        // get size after transforming photo
        $size = filesize($tmpFile);

        if ($storage->upload($tmpFile, $modifiedFile)) {
            unlink($tmpFile);

            return array($modifiedFile, $size);
        }

        return false;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function generateOriginalSaveName($file)
    {
        $fileName = uniqid(time() . substr(str_shuffle('abcdefABCDEF012345'), 0, 8));
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $savePath = sprintf('%s/%s/%s.%s', date('Y'), date('m'), $fileName, $extension);

        return $savePath;
    }

    /**
     * @param string $oldName
     * @param array $transform
     *
     * @return string
     */
    private function generateModifiedSaveName($oldName, $transform)
    {
        $newName = null;
        if (isset($transform['size'])) {
            $newName .= implode('x', $transform['size']);
        }

        if (isset($transform['rotate'][0])) {
            // We only need the angle for the name
            $newName .= sprintf('-r%s', $transform['rotate'][0]);
        }

        // Extract information from original file
        $directory = dirname($oldName);
        $originalName = pathinfo($oldName, PATHINFO_FILENAME);
        $extension = pathinfo($oldName, PATHINFO_EXTENSION);

        return FileUtils::normalizePath(sprintf('%s/%s-%s.%s', $directory, $originalName, $newName, $extension));
    }

    /**
     * @param array $array
     * @param $index
     *
     * @return array
     */
    private function arrayColumn(array $array, $index)
    {
        //$values = function_exists('array_column') ? array_column($array, $index) : array();
        return array_map(function ($item) use ($index) {
            return $item[$index];
        }, $array);
    }

    /**
     * @param array $photos
     *
     * @return PhotoCollection
     */
    private function createPhotoCollection(array $photos = array())
    {
        return new PhotoCollection($photos);
    }
}
