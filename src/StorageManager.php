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

use RuntimeException;
use SimplePhoto\Storage\StorageInterface;
use SimplePhoto\Toolbox\ArrayUtils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class StorageManager
{
    const FALLBACK_STORAGE = 'fallback';

    protected $default;

    /**
     * @var StorageInterface[]
     */
    protected $storageList = array();

    /**
     * @param $name
     * @param StorageInterface $storage
     * @throws RuntimeException
     */
    public function add($name, StorageInterface $storage)
    {
        if (isset($this->storageList[$name])) {
            throw new RuntimeException('Storage [' . $name . '] already exists.');
        }

        $this->storageList[$name] = $storage;
    }

    /**
     * @param $name
     * @param StorageInterface $storage
     */
    public function replace($name, StorageInterface $storage)
    {
        if ($this->has($name)) {
            $this->storageList[$name] = $storage;
        }
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
     * @return bool
     */
    public function has($name)
    {
        return isset($this->storageList[$name]);
    }

    /**
     * Checks if a fallback storage has been defined
     *
     * @return bool
     */
    public function hasFallback()
    {
        return $this->has(self::FALLBACK_STORAGE);
    }

    /**
     * @param $name
     * @return StorageInterface
     * @throws \RuntimeException
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \RuntimeException(
                'Photo storage [' . $name . '] does not exists.'
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
            return $this->default;
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
