<?php

namespace Jaccob\MediaBundle\Toolkit;

/**
 * Uses the convert system command
 */
class GDImageToolkit extends AbstractImageToolkit
{
    public function doScaleAndCrop($inFile, $outFile, $width, $height)
    {
        list($originalWidth, $originalHeight, $originalType) = getimagesize($inFile);

        switch ($originalType) {

            case IMAGETYPE_GIF:
                $inResource = imagecreatefromgif($inFile);
                break;

            case IMAGETYPE_JPEG:
                $inResource = imagecreatefromjpeg($inFile);
                break;

            case IMAGETYPE_PNG:
                $inResource = imagecreatefrompng($inFile);
                break;

            default:
                throw new \InvalidArgumentException(sprintf("%s: unsupported image type", $originalType));
        }

        if ($inResource === false) {
            throw new \InvalidArgumentException(sprintf("%s: could not load file", $inFile));
        }

        $originalRatio = $originalWidth / $originalHeight;
        $targetRatio = $width / $height;

        if ($originalRatio > $targetRatio) {
            $outHeight = $height;
            $outWidth = (int)($height * $originalRatio);
        } else {
            $outWidth = $width;
            $outHeight = (int)($width / $targetRatio);
        }

        // Resize the image into a temporary GD image
        $tempResource = imagecreatetruecolor($outWidth, $outHeight);
        imagecopyresampled($tempResource, $inResource, 0, 0, 0, 0, $outWidth, $outHeight, $originalWidth, $originalHeight);

        // Copy cropped region from temporary image into the desired GD image
        $x0 = ($outWidth - $width) / 2;
        $y0 = ($outHeight - $height) / 2;
        $outResource = imagecreatetruecolor($width, $height);
        imagecopy($outResource, $tempResource, 0, 0, $x0, $y0, $width, $height);
        imagejpeg($outResource, $outFile, 90);

        imagedestroy($inResource);
        imagedestroy($outResource);

        return true;
    }

    public function scaleAndCrop($inFile, $outFile, $width, $height)
    {
        $this->ensureFiles($inFile, $outFile);
        $this->doScaleAndCrop($inFile, $outFile, $width, $height);
    }

    /**
     * Scale image
     *
     * @param string $inFile
     * @param string $outFile
     * @param int $maxWidth
     * @param int $maxHeight
     * @param boolean $keepRatio
     */
    public function scaleTo($inFile, $outFile, $maxWidth = null, $maxHeight = null, $keepRatio = true)
    {
        $this->ensureFiles($inFile, $outFile);

        if (null === $maxHeight && null === $maxWidth) {
            throw new \LogicException("You must specify at least a height or a width");
        }

        list($originalWidth, $originalHeight, $originalType) = getimagesize($inFile);

        switch ($originalType) {

            case IMAGETYPE_GIF:
                $inResource = imagecreatefromgif($inFile);
                break;

            case IMAGETYPE_JPEG:
                $inResource = imagecreatefromjpeg($inFile);
                break;

            case IMAGETYPE_PNG:
                $inResource = imagecreatefrompng($inFile);
                break;

            default:
                throw new \InvalidArgumentException(sprintf("%s: unsupported image type", $originalType));
        }

        if ($inResource === false) {
            throw new \InvalidArgumentException(sprintf("%s: could not load file", $inFile));
        }

        if (null === $maxHeight) {
            $outWidth = (int)$maxWidth;
            $outHeight = ($originalHeight / $originalWidth) * $outWidth;
        } else if (null === $maxWidth) {
            $outHeight = (int)$maxHeight;
            $outWidth = ($originalWidth / $originalHeight) * $outHeight;
        } else {
            if (!$keepRatio) {
                return $this->doScaleAndCrop($inFile, $outFile, $maxWidth, $maxHeight);
            } else {
                $outWidth = (int)$outWidth;
                $outHeight = (int)$outHeight;
            }
        }

        $outResource = imagecreatetruecolor($outWidth, $outHeight);

        imagecopyresampled($outResource, $inResource, 0, 0, 0, 0, $outWidth, $outHeight, $originalWidth, $originalHeight);
        imagejpeg($outResource, $outFile, 90);
        imagedestroy($inResource);
        imagedestroy($outResource);

        return true;
    }
}
