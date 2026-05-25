<?php
namespace VisionPHP\Filters;

use VisionPHP\Core\Image;

/**
 * Thresholding operations — Binary, Inverse Binary, Otsu-like.
 */
class Threshold
{
    /**
     * Binary threshold: pixels above threshold → 255, below → 0.
     */
    public static function binary(Image $img, int $threshold = 128): Image
    {
        $src = $img->getResource();
        $w = imagesx($src);
        $h = imagesy($src);
        $dst = imagecreatetruecolor($w, $h);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($src, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $gray = (int)(($r + $g + $b) / 3);
                $val = $gray >= $threshold ? 255 : 0;
                $color = imagecolorallocate($dst, $val, $val, $val);
                imagesetpixel($dst, $x, $y, $color);
            }
        }

        $result = new Image();
        $result->setResource($dst);
        return $result;
    }

    /**
     * Inverse binary threshold.
     */
    public static function inverseBinary(Image $img, int $threshold = 128): Image
    {
        $src = $img->getResource();
        $w = imagesx($src);
        $h = imagesy($src);
        $dst = imagecreatetruecolor($w, $h);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($src, $x, $y);
                $gray = (int)((($rgb >> 16) & 0xFF + ($rgb >> 8) & 0xFF + $rgb & 0xFF) / 3);
                $val = $gray >= $threshold ? 0 : 255;
                $color = imagecolorallocate($dst, $val, $val, $val);
                imagesetpixel($dst, $x, $y, $color);
            }
        }

        $result = new Image();
        $result->setResource($dst);
        return $result;
    }

    /**
     * Otsu's method — Automatically finds optimal threshold using histogram analysis.
     */
    public static function otsu(Image $img): Image
    {
        $src = $img->getResource();
        $w = imagesx($src);
        $h = imagesy($src);

        // Build histogram
        $histogram = array_fill(0, 256, 0);
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($src, $x, $y);
                $gray = (int)((($rgb >> 16) & 0xFF + ($rgb >> 8) & 0xFF + $rgb & 0xFF) / 3);
                $histogram[$gray]++;
            }
        }

        $totalPixels = $w * $h;
        $bestThreshold = 0;
        $bestVariance = 0;
        $sumTotal = 0;
        for ($i = 0; $i < 256; $i++) $sumTotal += $i * $histogram[$i];

        $sumBg = 0;
        $weightBg = 0;

        for ($t = 0; $t < 256; $t++) {
            $weightBg += $histogram[$t];
            if ($weightBg == 0) continue;
            $weightFg = $totalPixels - $weightBg;
            if ($weightFg == 0) break;

            $sumBg += $t * $histogram[$t];
            $meanBg = $sumBg / $weightBg;
            $meanFg = ($sumTotal - $sumBg) / $weightFg;

            $variance = $weightBg * $weightFg * ($meanBg - $meanFg) ** 2;
            if ($variance > $bestVariance) {
                $bestVariance = $variance;
                $bestThreshold = $t;
            }
        }

        return self::binary($img, $bestThreshold);
    }
}
