<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Toolbox;

use SimplePhoto\Utils\FileUtils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class ImageTransformer
{
    /**
     * @var Image
     */
    protected $originalImage;

    /**
     * @var Image
     */
    protected $image;

    /**
     * Constructor
     *
     * @param string $file
     * @param Image $image Image Model
     *
     * @throws \RuntimeException
     */
    public function __construct($file, Image $image)
    {
        set_error_handler(function ($code, $message, $file, $line) {
            // Silence
        });

        $info = getimagesize(FileUtils::normalizePath($file));

        if (!isset($info['mime'])) {
            throw new \RuntimeException(sprintf(
                'Could not load image [%s];', $file
            ));
        }

        $image->setFile($file);
        $image->setWidth($info[0]);
        $image->setHeight($info[1]);
        $image->setType($info[2]);
        $image->setMime($info['mime']);

        $this->createResource($image);
        // $this->originalImage =
        $this->image = $image;

        restore_error_handler();
    }

    /**
     * @param int $width
     * @param int $height
     * @param Color $background
     *
     * @return resource
     */
    public function createImage($width, $height, $background = null)
    {
        if ($background == null) {
            $background = new Color(0xFF000000);
        }

        $opacity = (int) ceil($background->alpha() * 0.49803921568627);

        $resource = imagecreatetruecolor($width, $height);
        $color = imagecolorallocatealpha(
            $resource, $background->red(), $background->green(), $background->blue(), $opacity);
        imagefill($resource, 0, 0, $color);

        imagealphablending($resource, false);
        imagesavealpha($resource, true);

        return $resource;
    }

    /**
     * @param Image $image
     *
     * @throws \RuntimeException
     */
    public function createResource(Image $image)
    {
        // set resource
        switch ($image->getType()) {
            case IMG_PNG:
            case 3:
                $image->setResource(imagecreatefrompng($image->getFile()));
                break;
            case IMG_JPG:
            case 2:
                $image->setResource(imagecreatefromjpeg($image->getFile()));
                break;
            case IMG_GIF:
            case 1:
                $image->setResource(imagecreatefromgif($image->getFile()));
                break;
            default:
                throw new \RuntimeException(
                    'Invalid image type ({$image->getType()});');
                break;
        }
    }

    /**
     * Resize Image
     *
     * @param int $width
     * @param int $height
     * @param boolean $maintainRatio
     *
     * @return $this
     */
    public function resize($width, $height, $maintainRatio = true)
    {
        if ($maintainRatio) {
            $maxRatio = max($width / $this->image->getWidth(), $height / $this->image->getHeight());
            $width = $this->image->getWidth() * $maxRatio;
            $height = $this->image->getHeight() * $maxRatio;
        }

        $tmpImage = $this->createImage($width, $height, new Color(0xFFFFFFFF));

        if (imagecopyresampled(
            $tmpImage, $this->image->getResource(), 0, 0, 0, 0,
            $width, $height, $this->image->getWidth(), $this->image->getHeight())
        ) {
            imagedestroy($this->image->getResource());

            $this->image->setResource($tmpImage);
            $this->image->setWidth($width);
            $this->image->setHeight($height);
        }

        return $this;
    }

    /**
     * @param null|string $destination
     *
     * @return bool
     */
    public function save($destination = null)
    {
        switch ($this->image->getType()) {
            case IMG_JPEG:
            case IMG_JPG:
                $ret = imagejpeg($this->image->getResource(), $destination);
                break;
            case IMG_GIF:
                $ret = imagegif($this->image->getResource(), $destination);
                break;
            case IMG_PNG:
            default:
                $ret = imagepng($this->image->getResource(), $destination);
                break;
        }

        return $ret;
    }
}
