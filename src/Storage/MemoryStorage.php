<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Storage;

use SimplePhoto\Toolbox\FileUtils;

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

        if (substr($name, 0, 8) == ':memory:') {
            $name = substr($name, 8);
        }

        $hash = sprintf(':memory:%s', $name);
        $this->storage[$hash] = array(
            'content' => file_get_contents($file),
            'mtime' => time(),
            'mime' => FileUtils::getMimeFromExtension(FileUtils::getExtension($name)),
        );

        return $hash;
    }

    /**
     * {@inheritDoc}
     */
    public function getInfo($file)
    {
        if (!$this->exists($file)) {
            return false;
        }

        return array(
            'file_size' => FileUtils::getContentSize($this->storage[$file]['content'])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function deletePhoto($file)
    {
        if (!$this->exists($file)) {
            return true;
        }

        unset($this->storage[$file]);

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
        $file = $this->storage[$file];

        // There is no way to generate a valid url for displaying
        // memory storage, so we use the Data Uri Scheme
        // {@see http://en.wikipedia.org/wiki/Data_URI_scheme}
        // which you can use in <img> src attribute
        return 'data:' . $file['mime'] . ';base64,' . base64_encode($file['content']);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoResource($file, $tmpFile)
    {
        if ($this->exists($file)) {
            file_put_contents($tmpFile, $this->storage[$file]['content']);
        }

        return $tmpFile;
    }

    /**
     * {@inheritDoc}
     */
    public
    function exists($file)
    {
        return isset($this->storage[$file]);
    }
}
