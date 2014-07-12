<?php

namespace SimplePhoto\Storage;

use fXmlRpc\Client as XmlRpcClient;

/**
 * Gravatar based storage.
 * Note: This storage is limited to the account its initialized
 * with. A use for this storage could be that you want to display
 * your own image on your personal website... or whatever...
 *
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class GravatarStorage implements StorageInterface
{
    protected $email;

    protected $password;

    /**
     * @var int
     */
    protected $cacheDuration = 0;

    /**
     * @var XmlRpcClient
     */
    protected $client;

    /**
     * @var bool
     */
    protected $secure = true;

    /**
     * Constructor. Valid options include
     *  cache_duration: the cache duration to use for this instance
     *  password: the password for this account
     *  secure: set to true to enable secure urls
     *
     * @param $email
     * @param array $options
     */
    public function __construct($email, array $options = array())
    {
        $this->email = $email;

        // Gravatar docs:
        // "The email_hash GET parameter is the md5 hash of the users
        // email address after it has been lowercased, and trimmed."
        $this->emailHash = md5(strtolower(trim($email)));

        if (isset($options['password'])) {
            $this->password = $options['password'];
        }

        if (isset($options['cache_duration'])) {
            $this->cacheDuration = $options['cache_duration'];
        }

        if (isset($options['secure'])) {
            $this->secure = (bool) $options['secure'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function upload($file, $name, array $options = array())
    {

    }

    /**
     * {@inheritDoc}
     */
    public function getInfo($file)
    {
        return array();
    }

    /**
     * Delete photo file
     *
     * @param string $file
     * @return boolean
     */
    public function deletePhoto($file)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoPath($file)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoUrl($file)
    {
        return 'http' . ($this->secure ? 's' : '')
        . '://' . ($this->secure ? 'secure' : 'www')
        . '.gravatar.com/avatar/' . $this->emailHash;
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoResource($file, $tmpFile)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function exists($file)
    {
        return null;
    }

    /**
     * @return XmlRpcClient
     */
    private function createClient()
    {
        if (!$this->client) {
            $this->client = new XmlRpcClient('https://secure.gravatar.com/xmlrpc?user=' . $this->emailHash);
        }

        return $this->client;
    }

    private function call($method, $args = array())
    {

    }
}
 