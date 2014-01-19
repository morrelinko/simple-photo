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
class FilePathSource implements PhotoSourceInterface
{
    /**
     * @var string
     */
    protected $file;

    /**
     * {@inheritDocs}
     */
    public function process($photoData)
    {
        $this->file = $photoData;
    }

    /**
     * {@inheritDocs}
     */
    public function getName()
    {
        return basename($this->file);
    }

    /**
     * {@inheritDocs}
     */
    public function getFile()
    {
        return $this->file;
    }
}
