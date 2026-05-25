<?php

namespace VisionPHP\Core;

class Image
{
    private $gdImage = null;
    private $width = 0;
    private $height = 0;

    public function __construct(?string $filepath = null)
    {
        if ($filepath !== null) {
            $this->load($filepath);
        }
    }

    public function createBlank(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->gdImage = imagecreatetruecolor($width, $height);

        // Fill white
        $white = imagecolorallocate($this->gdImage, 255, 255, 255);
        imagefill($this->gdImage, 0, 0, $white);
    }

    private function load(string $filepath)
    {
        if (!file_exists($filepath)) {
            throw new \Exception("Image not found: $filepath");
        }

        $info = getimagesize($filepath);
        if ($info === false) {
            throw new \Exception("Invalid image file: $filepath");
        }

        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
                $this->gdImage = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $this->gdImage = imagecreatefrompng($filepath);
                break;
            case 'image/gif':
                $this->gdImage = imagecreatefromgif($filepath);
                break;
            default:
                throw new \Exception("Unsupported image type: $mime");
        }

        $this->width = imagesx($this->gdImage);
        $this->height = imagesy($this->gdImage);
    }

    public function save(string $filepath): bool
    {
        if ($this->gdImage === null) return false;

        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                return imagejpeg($this->gdImage, $filepath, 90);
            case 'png':
                return imagepng($this->gdImage, $filepath);
            case 'gif':
                return imagegif($this->gdImage, $filepath);
            default:
                throw new \Exception("Unsupported save extension: $ext");
        }
    }

    public function getResource()
    {
        return $this->gdImage;
    }

    public function setResource($gdImage)
    {
        $this->gdImage = $gdImage;
        $this->width = imagesx($gdImage);
        $this->height = imagesy($gdImage);
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function __destruct()
    {
        if ($this->gdImage !== null) {
            imagedestroy($this->gdImage);
        }
    }
}
