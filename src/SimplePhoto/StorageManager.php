<?php namespace SimplePhoto;

use SimplePhoto\Storage\StorageInterface;
use SimplePhoto\Utils\ArrayUtils;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class StorageManager
{
    protected $default;

    /**
     * @var StorageInterface[]
     */
    protected $storageList = array();

    /**
     * @param $name
     * @param StorageInterface $filesystem
     */
    public function add($name, StorageInterface $filesystem)
    {
        $this->storageList[$name] = $filesystem;
    }

    /**
     * @param $name
     *
     * @return StorageInterface
     * @throws \RuntimeException
     */
    public function get($name)
    {
        if (!isset($this->storageList[$name])) {
            throw new \RuntimeException(
                "Photo storage [{$name}] does not exists."
            );
        }

        return $this->storageList[$name];
    }

    /**
     * @param $name
     */
    public function remove($name)
    {
        if (isset($this->storageList[$name])) {
            unset($this->storageList[$name]);
        }
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        if ($this->default != null) {
            return $this->get($this->default);
        }

        return ArrayUtils::first(array_keys($this->storageList));
    }

    /**
     * @param string $name
     */
    public function setDefault($name)
    {
        $this->default = $name;
    }
} 