<?php

namespace VisionPHP;

use VisionPHP\Core\Image;
use VisionPHP\Filters\Grayscale;
use VisionPHP\Filters\Blur;
use VisionPHP\Filters\Threshold;
use VisionPHP\Features\EdgeDetection;
use VisionPHP\Transform\Transform;
use VisionPHP\Drawing\Draw;

class VisionPHP
{
    // ──────────── Image I/O ────────────

    public static function imread(string $filepath): Image
    {
        return new Image($filepath);
    }

    public static function create(int $width, int $height): Image
    {
        $img = new Image();
        $img->createBlank($width, $height);
        return $img;
    }

    public static function imwrite(string $filepath, Image $img): bool
    {
        return $img->save($filepath);
    }

    // ──────────── Filters ────────────

    public static function cvtColor(Image $img, string $mode = 'GRAY'): Image
    {
        if (strtoupper($mode) === 'GRAY') {
            return Grayscale::apply($img);
        }
        throw new \Exception("Mode $mode not supported yet.");
    }

    public static function GaussianBlur(Image $img, int $radius = 3): Image
    {
        return Blur::gaussian($img, $radius);
    }

    public static function Sobel(Image $img): Image
    {
        return EdgeDetection::sobel($img);
    }

    // ──────────── Thresholding ────────────

    public static function threshold(Image $img, int $value = 128): Image
    {
        return Threshold::binary($img, $value);
    }

    public static function thresholdInverse(Image $img, int $value = 128): Image
    {
        return Threshold::inverseBinary($img, $value);
    }

    public static function thresholdOtsu(Image $img): Image
    {
        return Threshold::otsu($img);
    }

    // ──────────── Transform ────────────

    public static function resize(Image $img, int $width, int $height): Image
    {
        return Transform::resize($img, $width, $height);
    }

    public static function crop(Image $img, int $x, int $y, int $width, int $height): Image
    {
        return Transform::crop($img, $x, $y, $width, $height);
    }

    public static function rotate(Image $img, float $angle): Image
    {
        return Transform::rotate($img, $angle);
    }

    public static function flipH(Image $img): Image
    {
        return Transform::flipHorizontal($img);
    }

    public static function flipV(Image $img): Image
    {
        return Transform::flipVertical($img);
    }

    // ──────────── Drawing ────────────

    public static function rectangle(Image $img, int $x1, int $y1, int $x2, int $y2, array $color = [0, 255, 0], int $thickness = 2): Image
    {
        return Draw::rectangle($img, $x1, $y1, $x2, $y2, $color, $thickness);
    }

    public static function circle(Image $img, int $cx, int $cy, int $r, array $color = [255, 0, 0], int $thickness = 2): Image
    {
        return Draw::circle($img, $cx, $cy, $r, $color, $thickness);
    }

    public static function line(Image $img, int $x1, int $y1, int $x2, int $y2, array $color = [255, 255, 255], int $thickness = 1): Image
    {
        return Draw::line($img, $x1, $y1, $x2, $y2, $color, $thickness);
    }

    public static function putText(Image $img, string $text, int $x, int $y, array $color = [255, 255, 255], int $size = 5): Image
    {
        return Draw::text($img, $text, $x, $y, $color, $size);
    }
}
