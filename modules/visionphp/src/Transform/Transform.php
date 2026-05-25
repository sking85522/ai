<?php
namespace VisionPHP\Transform;

use VisionPHP\Core\Image;

/**
 * Image transformation operations — Resize, Crop, Rotate, Flip.
 */
class Transform
{
    public static function resize(Image $img, int $newWidth, int $newHeight): Image
    {
        $src = $img->getResource();
        $dst = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($src), imagesy($src));
        $result = new Image();
        $result->setResource($dst);
        return $result;
    }

    public static function crop(Image $img, int $x, int $y, int $width, int $height): Image
    {
        $src = $img->getResource();
        $dst = imagecreatetruecolor($width, $height);
        imagecopy($dst, $src, 0, 0, $x, $y, $width, $height);
        $result = new Image();
        $result->setResource($dst);
        return $result;
    }

    public static function rotate(Image $img, float $angle, int $bgColor = 0): Image
    {
        $src = $img->getResource();
        $rotated = imagerotate($src, $angle, $bgColor);
        $result = new Image();
        $result->setResource($rotated);
        return $result;
    }

    public static function flipHorizontal(Image $img): Image
    {
        $src = $img->getResource();
        imageflip($src, IMG_FLIP_HORIZONTAL);
        $result = new Image();
        $result->setResource($src);
        return $result;
    }

    public static function flipVertical(Image $img): Image
    {
        $src = $img->getResource();
        imageflip($src, IMG_FLIP_VERTICAL);
        $result = new Image();
        $result->setResource($src);
        return $result;
    }
}
