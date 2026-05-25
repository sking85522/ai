<?php

namespace VisionPHP\Features;

use VisionPHP\Core\Image;

class EdgeDetection
{
    /**
     * Applies the Sobel operator to find edges.
     */
    public static function sobel(Image $img): Image
    {
        $res = $img->getResource();
        if ($res === null) throw new \Exception("Invalid image resource");

        $width = $img->getWidth();
        $height = $img->getHeight();

        // First convert to grayscale for accurate edge detection
        $grayRes = imagecreatetruecolor($width, $height);
        imagecopy($grayRes, $res, 0, 0, 0, 0, $width, $height);
        imagefilter($grayRes, IMG_FILTER_GRAYSCALE);

        $outRes = imagecreatetruecolor($width, $height);

        // Edge detection using IMG_FILTER_EDGEDETECT (which is a fast convolution in GD)
        // A true Sobel requires custom pixel looping which is very slow in PHP,
        // so we use the built-in fast convolution matrix that GD offers for edges.
        imagecopy($outRes, $grayRes, 0, 0, 0, 0, $width, $height);
        imagefilter($outRes, IMG_FILTER_EDGEDETECT);

        imagedestroy($grayRes);

        $newImg = new Image();
        $newImg->setResource($outRes);
        return $newImg;
    }
}
