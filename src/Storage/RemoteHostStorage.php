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

use SimplePhoto\Utils\FileUtils;
use SimplePhoto\Utils\TextUtils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class RemoteHostStorage implements StorageInterface
{
    /**
     * @var string
     */
    protected $host;

    /**
     * Ftp Username
     *
     * @var string
     */
    protected $username = 'anonymous';

    /**
     * Ftp Password
     *
     * @var string
     */
    protected $password = '';

    /**
     * @var int
     */
    protected $port = 21;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $root;

    /**
     * Ftp connection
     *
     * @var
     */
    protected $connection;

    /**
     * Constructor
     *
     * @param array $options Available options
     * <pre>
     * path: save photos path relative to {$options#root} on host eg files/photos
     * host: FTP Hostname
     * url: Remote host url eg http://example.com
     * root: Root directory to web host eg /var/www/public_html (Required)
     * port: Port number defaults to 21 (Optional)
     * </pre>
     */
    public function __construct(array $options = array())
    {
        foreach ($options as $option => $value) {
            switch ($option) {
                case 'path':
                    $this->path = $value;
                    break;
                case 'host':
                    $this->host = $value;
                    break;
                case 'url':
                    $this->url = $value;
                    break;
                case 'root':
                    $this->root = $value;
                    break;
                case 'port':
                    $this->port = (int) $value;
                    break;
                case 'username':
                    $this->username = $value;
                    break;
                case 'password':
                    $this->password = $value;
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritDoc}
     */
    public function upload($file, $destination, array $options = array())
    {
        if (!is_file($file)) {
            throw new \RuntimeException(
                'Unable to upload; File ' . $file . ' does not exists.'
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
        $this->verifyPathExists(dirname($savePath), true);

        $this->chdir(dirname($savePath));
        $result = ftp_put($this->connection(), basename($savePath), $file, FTP_BINARY);
        $this->chdir($this->path);

        return $result === false
            ? false
            : $this->normalizePath($destination, false, false);
    }

    /**
     * {@inheritDoc}
     */
    public function deletePhoto($file)
    {
        $file = $this->normalizePath($file, true, true);
        if (!$this->exists($file)) {
            return true;
        }

        return ftp_delete($this->connection(), $file);
    }

    /**
     * @param string $file
     *
     * @return mixed
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
        return rtrim($this->url . '/' . $this->path . '/' . $file, '/');
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoResource($file)
    {
        $tmpName = tempnam(sys_get_temp_dir(), 'temp');
        ftp_get(
            $this->connection(),
            $tmpName,
            $this->normalizePath($file, true),
            FTP_ASCII
        );

        return $tmpName;
    }

    /**
     * Lifted from the gaufrette library
     *
     * {@inheritDoc}
     */
    public function exists($file)
    {
        $lines = ftp_rawlist($this->connection(), '-1t ' . dirname($file));
        if (false === $lines) {
            return false;
        }

        $pattern = '{(?<!->) ' . preg_quote(basename($file)) . '( -> |$)}m';
        foreach ($lines as $line) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }

        return false;
    }

    public function close()
    {
        if ($this->connection != null) {
            ftp_close($this->connection);
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param string $path
     * @param   bool $createIfNotExists
     *
     * @return string
     * @throws \RuntimeException
     */
    public function verifyPathExists($path, $createIfNotExists = false)
    {
        if (!$this->directoryExists($path)) {
            if ($createIfNotExists) {
                $this->createDirectory($path);
            } else {
                throw new \RuntimeException(sprintf(
                    'Directory: %s not found',
                    $path
                ));
            }
        }

        return $path;
    }

    /**
     * Checks if we have a valid Ftp connection
     *
     * @return bool
     */
    public function isConnected()
    {
        return $this->connection != null;
    }

    /**
     * Creates Ftp Connection
     *
     * @return resource
     * @throws \RuntimeException
     */
    public function connection()
    {
        if ($this->connection == null) {
            $this->connect();
        }

        return $this->connection;
    }

    public function connect()
    {
        // Open Ftp Connection
        $this->connection = ftp_connect($this->host, $this->port);
        if ($this->connection == null) {
            throw new \RuntimeException(sprintf(
                'Could not connect to host \'%s\' on port: %s.',
                $this->host,
                $this->port
            ));
        }

        // Login
        if (ftp_login($this->connection, $this->username, $this->password) === false) {
            $this->close();
            throw new \RuntimeException(sprintf(
                'Unable to login as %s.',
                $this->username
            ));
        }
    }

    /**
     * Creates the specified directory recursively
     *
     * @param string $directory Directory to create
     *
     * @return bool
     * @throws \RuntimeException if the directory could not be created
     */
    public function createDirectory($directory)
    {
        // create parent directory if needed
        $parent = dirname($directory);
        if (!$this->directoryExists($parent)) {
            $this->createDirectory($parent);
        }

        if (ftp_mkdir($this->connection(), $directory) === false) {
            throw new \RuntimeException(sprintf(
                'Could not create directory \'%s\'.',
                $directory
            ));
        }

        return true;
    }

    /**
     * @param string $directory
     *
     * @return bool
     */
    public function directoryExists($directory)
    {
        if ($directory === '/') {
            return true;
        }

        if (!$this->chdir($directory)) {
            return false;
        }

        $this->chdir($this->path);

        return true;
    }

    /**
     * @param $directory
     *
     * @return bool
     */
    public function chdir($directory)
    {
        if (@ftp_chdir($this->connection(), $directory) === false) {
            return false;
        }

        return true;
    }

    /**
     * @param $path
     * @param bool $withRoot
     * @param bool $withPath
     *
     * @return mixed
     */
    protected function normalizePath($path, $withRoot = false, $withPath = true)
    {
        $path = ($withRoot ? $this->root : null) .
            FileUtils::normalizePath(($withPath ? $this->path . '/' : '') . '/' . $path);

        return $path;
    }
}
