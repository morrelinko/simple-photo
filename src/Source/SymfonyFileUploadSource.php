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

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class SymfonyFileUploadSource implements PhotoSourceInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected $file;

    /**
     * Construct
     *
     * @param UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->file->getClientOriginalName();
    }

    /**
     * {@inheritDoc}
     */
    public function getFile()
    {
        return $this->file->getRealPath();
    }

    /**
     * {@inheritDoc}
     */
    public function getMime()
    {
        return $this->file->getMimeType();
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        return $this->file->isValid();
    }
}
