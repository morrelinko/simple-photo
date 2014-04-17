<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Transformer;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use SimplePhoto\Toolbox\FileUtils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class DefaultTransformer implements TransformerInterface
{
    /**
     * @var \Imagine\Gd\Imagine
     */
    protected $imagine;

    /**
     * @param Imagine $imagine
     */
    public function __construct(Imagine $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($file, array $transformOptions = array(), array $parameters = array())
    {
        $image = $this->imagine->open($file);

        foreach ($transformOptions as $transform => $args) {
            if ($transform == 'resize') {
                $image->resize(new Box($args[0], $args[1]));
            } else if ($transform == 'rotate') {
                list($angle, $background) = array_pad($transform['rotate'], 2, null);
                $image->rotate((int) $angle, $background);
            }
        }

        $image->save($file, array(
            'format' => FileUtils::getExtensionFromMime($parameters['mime_type'])
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function generateName(array $transformOptions = array())
    {
        $name = '';
        foreach ($transformOptions as $transform => $args) {
            if ($transform == 'resize') {
                $name .= implode('x', $args);
            } else if ($transform == 'rotate') {
                // We only need the angle added to the generated name
                $name .= sprintf('-r%s', $args[0]);
            }
        }

        return $name;
    }
}
