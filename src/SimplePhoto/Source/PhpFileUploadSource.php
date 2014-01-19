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

    /**
     * {@inheritDocs}
     */
    public function process($photoData)
    {
        $this->fileData = $photoData;
    }

    /**
     * {@inheritDocs}
     */
    public function getName()
    {
        return $this->fileData['name'];
    }

    /**
     * {@inheritDocs}
     */
    public function getFile()
    {
        return $this->fileData['tmp_name'];
    }
}
