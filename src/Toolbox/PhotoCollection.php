<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Toolbox;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class PhotoCollection implements \Countable
{
    protected $photos = array();

    /**
     * Constructor
     *
     * @param array $photos
     */
    public function __construct(array $photos = array())
    {
        $this->photos = $photos;
    }

    /**
     * Get all of the photos in the collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->photos;
    }

    /**
     * Push a an onto the end of the collection.
     *
     * @param  mixed $photo
     *
     * @return void
     */
    public function push($photo)
    {
        $this->photos[] = $photo;
    }

    /**
     * Get a photo from the collection by key.
     *
     * @param  mixed $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->photos[$key];
        }

        return null;
    }

    /**
     * Checks if a photo exists in the collection.
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->photos);
    }

    /**
     * Executes a callback on each photo in the collection
     * Setting each item to the result from the callback.
     *
     * @param callable $callback
     *
     * @return PhotoCollection
     */
    public function transform(\Closure $callback)
    {
        $this->photos = array_map($callback, $this->photos);

        return $this;
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  \Closure $callback
     *
     * @return PhotoCollection
     */
    public function filter(\Closure $callback)
    {
        return new static(array_filter($this->photos, $callback));
    }

    /**
     * Gets values from each item of the collection
     *
     * @param \Closure $callback
     *
     * @return array
     */
    public function lists(\Closure $callback)
    {
        return array_map($callback, $this->photos);
    }

    /**
     * @return PhotoCollection
     */
    public function ksort()
    {
        ksort($this->photos);

        return $this;
    }

    /**
     * Checks if this collection has no photos
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->photos);
    }

    /**
     * @see \Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->photos);
    }
}
