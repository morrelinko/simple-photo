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

use SimplePhoto\Toolbox\FileUtils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class FilePathSource implements PhotoSourceInterface
{
    /**
     * @var string
     */
    protected $file;

    public function __construct($file = null)
    {
        $this->file = $file;
    }

    /**
     * {@inheritDoc}
     */
    public function process(array $options = array())
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return basename($this->file);
    }

    /**
     * {@inheritDoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritDoc}
     */
    public function getMime()
    {
        return FileUtils::getMime($this->file);
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        return is_file($this->file);
    }
}
