<?php

namespace VisionPHP\Filters;

use VisionPHP\Core\Image;

class Grayscale
{
    public static function apply(Image $img): Image
    {
        $res = $img->getResource();
        if ($res === null) throw new \Exception("Invalid image resource");

        $width = $img->getWidth();
        $height = $img->getHeight();

        $newRes = imagecreatetruecolor($width, $height);

        // Fast GD filter for grayscale
        imagecopy($newRes, $res, 0, 0, 0, 0, $width, $height);
        imagefilter($newRes, IMG_FILTER_GRAYSCALE);

        $newImg = new Image();
        $newImg->setResource($newRes);
        return $newImg;
    }
}
