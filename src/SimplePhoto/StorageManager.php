<?php namespace SimplePhoto;

use SimplePhoto\Storage\StorageInterface;
use SimplePhoto\Utils\ArrayUtils;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class StorageManager
{
    const FALLBACK_STORAGE = "fallback";

    protected $default;

    /**
     * @var StorageInterface[]
     */
    protected $storageList = array();

    /**
     * @param $name
     * @param StorageInterface $storage
     */
    public function add($name, StorageInterface $storage)
    {
        $this->storageList[$name] = $storage;
    }

    /**
     * @param StorageInterface $storage
     */
    public function setFallback(StorageInterface $storage)
    {
        $this->add(self::FALLBACK_STORAGE, $storage);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->storageList[$name]);
    }

    /**
     * @param $name
     *
     * @return StorageInterface
     * @throws \RuntimeException
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \RuntimeException(
                "Photo storage [{$name}] does not exists."
            );
        }

        return $this->storageList[$name];
    }

    /**
     * Get all defined storage
     *
     * @return Storage\StorageInterface[]
     */
    public function getAll()
    {
        return $this->storageList;
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