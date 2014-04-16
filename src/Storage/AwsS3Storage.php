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

use Aws\S3\S3Client;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class AwsS3Storage implements StorageInterface
{
    protected $client;

    protected $bucket;

    protected $directory;

    protected $acl = 'public-read';

    public function __construct(S3Client $s3Client, array $options = array())
    {
        $this->client = $s3Client;

        foreach ($options as $option => $value) {
            switch ($option) {
                case 'bucket':
                    $this->bucket = $value;
                    break;
                case 'directory':
                    $this->directory = $value;
                    break;
                case 'acl':
                    $this->acl = $value;
                    break;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function upload($file, $name, array $options = array())
    {
        try {
            $this->client->putObject($this->getOptions($file, array(
                'SourceFile' => $file
            )));

            return $this->normalizePath($name, false);

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getInfo($file)
    {
        $result = $this->client->headObject($this->getOptions($file));

        return array(
            'file_size' => $result['ContentLength']
        );
    }

    /**
     * {@inheritDoc}
     */
    public function deletePhoto($file)
    {
        return $this->client->deleteObject($this->getOptions($file));
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoPath($file)
    {
        return $this->normalizePath($file);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoUrl($file)
    {
        return $this->client->getObjectUrl(
            $this->bucket,
            $this->normalizePath($file)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getPhotoResource($file)
    {
        $tmpName = tempnam(sys_get_temp_dir(), 'temp');
        $this->client->getObject($this->getOptions($file), array(
            'SaveAs' => $tmpName
        ));

        return $tmpName;
    }

    /**
     * {@inheritDoc}
     */
    public function exists($file)
    {
        return $this->client->doesObjectExist($this->bucket, $this->normalizePath($file));
    }

    /**
     * {@inheritDoc}
     */
    protected function normalizePath($key, $prependDirectory = true)
    {
        return (empty($this->directory) || !$prependDirectory) ? $key : $this->directory . '/' . $key;
    }

    /**
     * @param $file
     * @param array $overrides
     * @return array
     */
    protected function getOptions($file, array $overrides = array())
    {
        $options['ACL'] = $this->acl;
        $options['Bucket'] = $this->bucket;
        $options['Key'] = $this->normalizePath($file);

        return array_merge($options, $overrides);
    }
}
