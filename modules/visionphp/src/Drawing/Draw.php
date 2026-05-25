<?php
namespace VisionPHP\Drawing;

use VisionPHP\Core\Image;

/**
 * Drawing functions — Rectangle, Circle, Line, Text overlay on images.
 */
class Draw
{
    public static function rectangle(Image $img, int $x1, int $y1, int $x2, int $y2, array $color = [0, 255, 0], int $thickness = 2): Image
    {
        $gd = $img->getResource();
        $c = imagecolorallocate($gd, $color[0], $color[1], $color[2]);
        for ($t = 0; $t < $thickness; $t++) {
            imagerectangle($gd, $x1 + $t, $y1 + $t, $x2 - $t, $y2 - $t, $c);
        }
        $result = new Image();
        $result->setResource($gd);
        return $result;
    }

    public static function filledRectangle(Image $img, int $x1, int $y1, int $x2, int $y2, array $color = [0, 255, 0]): Image
    {
        $gd = $img->getResource();
        $c = imagecolorallocate($gd, $color[0], $color[1], $color[2]);
        imagefilledrectangle($gd, $x1, $y1, $x2, $y2, $c);
        $result = new Image();
        $result->setResource($gd);
        return $result;
    }

    public static function circle(Image $img, int $cx, int $cy, int $radius, array $color = [255, 0, 0], int $thickness = 2): Image
    {
        $gd = $img->getResource();
        $c = imagecolorallocate($gd, $color[0], $color[1], $color[2]);
        imagesetthickness($gd, $thickness);
        imageellipse($gd, $cx, $cy, $radius * 2, $radius * 2, $c);
        $result = new Image();
        $result->setResource($gd);
        return $result;
    }

    public static function line(Image $img, int $x1, int $y1, int $x2, int $y2, array $color = [255, 255, 255], int $thickness = 1): Image
    {
        $gd = $img->getResource();
        $c = imagecolorallocate($gd, $color[0], $color[1], $color[2]);
        imagesetthickness($gd, $thickness);
        imageline($gd, $x1, $y1, $x2, $y2, $c);
        $result = new Image();
        $result->setResource($gd);
        return $result;
    }

    public static function text(Image $img, string $text, int $x, int $y, array $color = [255, 255, 255], int $fontSize = 5): Image
    {
        $gd = $img->getResource();
        $c = imagecolorallocate($gd, $color[0], $color[1], $color[2]);
        imagestring($gd, $fontSize, $x, $y, $text, $c);
        $result = new Image();
        $result->setResource($gd);
        return $result;
    }
}
