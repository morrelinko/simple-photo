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

use SimplePhoto\Toolbox\BaseUrlInterface;
use SimplePhoto\Toolbox\FileUtils;
use SimplePhoto\Toolbox\HttpBaseUrl;
use SimplePhoto\Toolbox\TextUtils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class LocalStorage implements StorageInterface
{
    /**
     * @var string
     */
    protected $projectRoot;

    /**
     * @var null|string
     */
    protected $savePath;

    /**
     * @var \SimplePhoto\Toolbox\BaseUrlInterface
     */
    protected $baseUrlImpl;

    /**
     * Constructor
     *
     * @param array $options
     * <pre>
     * root: Project public directory
     * path: Path to save photos
     * </pre>
     * @param \SimplePhoto\Toolbox\BaseUrlInterface $baseUrlImpl
     */
    public function __construct(array $options = array(), BaseUrlInterface $baseUrlImpl = null)
    {
        foreach ($options as $option => $value) {
            switch ($option) {
                case 'root':
                    $this->projectRoot = FileUtils::normalizePath($value);
                    break;
                case 'path':
                    $this->savePath = $value;
                    break;
            }
        }

        $this->baseUrlImpl = $baseUrlImpl;
    }

    /**
     * {@inheritDoc}
     */
    public function upload($file, $destination, array $options = array())
    {
        if (!is_file($file)) {
            throw new \RuntimeException(
                'Unable to upload; File [{$file}] does not exists.'
            );
        }

        $fileName = basename($file);
        if ($destination) {
            if (TextUtils::endsWith($destination, '/')) {
                $destination = $destination . $fileName;
            }
        } else {
            $destination = $fileName;
        }

        $savePath = $this->normalizePath($destination, true);
        $this->verifyPathExists(dirname($this->normalizePath($savePath, true)), true);

        if (copy($file, $savePath)) {
            return $this->normalizePath($destination, false, false);
        }

        return false;
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
            'file_size' => filesize($this->normalizePath($file))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function deletePhoto($file)
    {
        if (!$this->exists($file)) {
            // If the file does not exists,
            // it is considered deleted
            return true;
        }

        // Delete from file system
        if (unlink($this->normalizePath($file, true, true))) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoPath($file)
    {
        return $this->normalizePath($file, true, true);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoUrl($file)
    {
        if ($this->baseUrlImpl == null) {
            $this->baseUrlImpl = new HttpBaseUrl();
        }

        $basePath = $this->projectRoot . '/' . $this->savePath;
        $filePath = ltrim(preg_replace('!^' . $this->projectRoot . '/?!', '', $file), '/');
        $path = FileUtils::normalizePath($basePath . '/' . $filePath);

        return rtrim(str_replace($this->projectRoot, $this->baseUrlImpl->getBaseUrl(), $path), '/');
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoResource($file)
    {
        $tmpName = tempnam(sys_get_temp_dir(), 'temp');
        copy($this->normalizePath($file, true), $tmpName);

        return $tmpName;
    }

    /**
     * {@inheritDoc}
     */
    public function exists($file)
    {
        $file = $this->normalizePath($file, true, true);

        return file_exists($file) && is_file($file);
    }

    /**
     * @return mixed
     */
    public function getSavePath()
    {
        return $this->savePath;
    }

    /**
     * @param $savePath
     */
    public function setSavePath($savePath)
    {
        $this->savePath = $savePath;
    }

    /**
     * Gets the full path for saving photo
     *
     * @return string
     */
    public function getPath()
    {
        return $this->normalizePath(null, true, true);
    }

    /**
     * @param $directory
     * @return bool
     */
    public function directoryExists($directory)
    {
        return is_dir($this->normalizePath($directory));
    }

    /**
     * @param $directory
     * @param bool $recursive
     * @param int $mode
     * @return bool
     */
    public function createDirectory($directory, $recursive = true, $mode = 0777)
    {
        if ($this->directoryExists($directory)) {
            return true;
        }

        if (mkdir($this->normalizePath($directory), $mode, $recursive)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $path
     * @param   bool $createIfNotExists
     * @return string
     * @throws \RuntimeException
     */
    public function verifyPathExists($path, $createIfNotExists = false)
    {
        if (!is_dir($path) && !$createIfNotExists) {
            throw new \RuntimeException(sprintf(
                'Directory: %s not found',
                $path
            ));
        }

        if ($createIfNotExists) {
            $this->createDirectory($path);
        }

        return $path;
    }

    /**
     * @param $path
     * @param bool $withRoot Set to true to prepend project root to the normalized path
     * @param $withBasePath
     * @return string
     */
    public function normalizePath($path, $withRoot = false, $withBasePath = true)
    {
        $dir = null;
        if (!FileUtils::isAbsolute($path)) {
            $dir = ($withRoot ? $this->projectRoot . '/' : null) .
                ($withBasePath ? $this->savePath . '/' : null);
        }

        return FileUtils::normalizePath($dir . $path);
    }
}
