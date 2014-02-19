<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Source;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class PhpFileUploadSource implements PhotoSourceInterface
{
    protected $fileData;

    public function __construct($fileData)
    {
        if ($fileData != null) {
            $this->process($fileData);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function process($fileData)
    {
        $this->fileData = $fileData;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->fileData['name'];
    }

    /**
     * {@inheritDoc}
     */
    public function getFile()
    {
        return $this->fileData['tmp_name'];
    }

    /**
     * {@inheritDoc}
     */
    public function getMime()
    {
        return $this->fileData['type'];
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        return true;
    }
}
