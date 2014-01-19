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

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class Color
{
    /**
     * @var int
     */
    protected $red;

    /**
     * @var int
     */
    protected $green;

    /**
     * @var int
     */
    protected $blue;

    /**
     * @var int
     */
    protected $alpha;

    /**
     * @param string|array|int $value
     */
    public function __construct($value)
    {
        switch (true) {
            case (is_string($value)):
                $this->createFromString($value);
                break;
            case (is_numeric($value)):
                $this->createFromInt($value);
                break;
            case (is_array($value)):
                $this->createFromArray($value);
                break;
            case ($value instanceof self):

                break;
        }
    }

    /**
     * @return int
     */
    public function alpha()
    {
        return $this->alpha;
    }

    /**
     * @return int
     */
    public function blue()
    {
        return $this->blue;
    }

    /**
     * @return int
     */
    public function green()
    {
        return $this->green;
    }

    /**
     * @return int
     */
    public function red()
    {
        return $this->red;
    }

    /**
     * Tries to generate a negative color from the one provided
     *
     * @return Color
     */
    public function negative()
    {
        $color = new self(array(
            $red = (0xFF - $this->red),
            $green = (0xFF - $this->green),
            $blue = (0xFF - $this->blue)
        ));

        return $color;
    }

    /**
     * Lightens a color
     *
     * @param int $percentage
     *
     * @return Color
     */
    public function lighten($percentage)
    {
        $steps = (int) floor(2.55 * $percentage);

        $color = new Color(array(
            $red = max(0, min(255, $this->red + $steps)),
            $green = max(0, min(255, $this->green + $steps)),
            $blue = max(0, min(255, $this->blue + $steps)),
            $alpha = $this->alpha
        ));

        return $color;
    }

    /**
     * Darkens a color
     *
     * @param int $percentage
     *
     * @return Color
     */
    public function darken($percentage)
    {
        $steps = (int) floor(2.55 * $percentage);

        $color = new Color(array(
            $red = max(0, min(255, $this->red - $steps)),
            $green = max(0, min(255, $this->green - $steps)),
            $blue = max(0, min(255, $this->blue - $steps)),
            $alpha = $this->alpha
        ));

        return $color;
    }

    /**
     * Gets the color in HEX Format
     *
     * @return string
     */
    public function toHex()
    {
        return str_pad(dechex($this->red), 2, '0', STR_PAD_LEFT)
        . str_pad(dechex($this->green), 2, '0', STR_PAD_LEFT)
        . str_pad(dechex($this->blue), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Gets the color in RGB Format
     *
     * @throws \LogicException
     */
    public function toRgb()
    {
        throw new \LogicException('Color.toRgb() not implemented.');
    }

    /**
     * @throws \LogicException
     */
    public function toHsl()
    {
        throw new \LogicException('Color.toHsl() not implemented.');
    }

    public function createFromString($value)
    {
        if (substr($value, 0, 1) == '#') {
            $value = substr($value, 1);
        }

        switch (strlen($value)) {
            case 3:
                $dec = hexdec(str_repeat($value[0], 2) . str_repeat($value[1], 2) . str_repeat($value[2], 2));
                break;
            case 6:
                $dec = hexdec($value);
                break;
            case 8:
                $dec = hexdec(substr($value, 0, 2)) . hexdec(substr($value, 2));
                break;
            default:
                throw new \LogicException(sprintf(
                    'Invalid Hex value [%s] in %s', $value, __METHOD__));
                break;
        }

        $this->createFromInt($dec);

        return $this;
    }

    public function createFromInt($value)
    {
        $value = min(array($value, 0xFFFFFFFF));

        $this->alpha = 0xFF & ($value >> 24);
        $this->red = 0xFF & ($value >> 16);
        $this->green = 0xFF & ($value >> 8);
        $this->blue = 0xFF & $value;

        return $this;
    }

    public function createFromArray($value)
    {
        $this->red = $value[0];
        $this->green = $value[1];
        $this->blue = $value[2];

        $this->alpha = isset($value[3]) ? $value[3] : dechex('FF');
    }
}
