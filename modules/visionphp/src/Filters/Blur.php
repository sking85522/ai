<?php

namespace VisionPHP\Filters;

use VisionPHP\Core\Image;

class Blur
{
    public static function gaussian(Image $img, int $passes = 3): Image
    {
        $res = $img->getResource();
        if ($res === null) throw new \Exception("Invalid image resource");

        $width = $img->getWidth();
        $height = $img->getHeight();

        $newRes = imagecreatetruecolor($width, $height);
        imagecopy($newRes, $res, 0, 0, 0, 0, $width, $height);

        // Apply Gaussian Blur via GD filter multiple times for strength
        for ($i = 0; $i < $passes; $i++) {
            imagefilter($newRes, IMG_FILTER_GAUSSIAN_BLUR);
        }

        $newImg = new Image();
        $newImg->setResource($newRes);
        return $newImg;
    }
}
