<?php

namespace SimplePhoto\Storage;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class MemoryStorage implements StorageInterface
{
    /**
     * @var array
     */
    protected $storage = array();

    /**
     * {@inheritDoc}
     */
    public function upload($file, $name, array $options = array())
    {
        if (!is_file($file)) {
            throw new \RuntimeException(
                'Unable to upload; File [' . $file . '] does not exists.'
            );
        }

        $this->storage[$file] = file_get_contents($file);

        return $file;
    }

    /**
     * {@inheritDoc}
     */
    public function deletePhoto($file)
    {
        if (!$this->exists($file)) {
            return true;
        }

        unset($this->storage);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoPath($file)
    {
        return $file;
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoUrl($file)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoResource($file)
    {
        return $this->exists($file) ? $this->storage[$file] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function exists($file)
    {
        return isset($this->storage[$file]);
    }
}
