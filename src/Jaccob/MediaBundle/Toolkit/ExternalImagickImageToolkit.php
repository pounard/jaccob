<?php

namespace Jaccob\MediaBundle\Toolkit;

/**
 * Uses the convert system command
 */
class ExternalImagickImageToolkit extends AbstractImageToolkit
{
    public function doScaleAndCrop($inFile, $outFile, $width, $height)
    {
        $size = ((int)$width) . "x" . ((int)$height);

        $command = array(
            escapeshellcmd("convert"),
            escapeshellarg($inFile),
            "-auto-orient",
            "-resize",
            "'" . $size . "^'",
            "-gravity",
            "center",
            "-crop",
            "'" . $size . "+0+0'",
            escapeshellarg($outFile),
        );

        $ret = 0;
        system(implode(" ", $command), $ret);

        if (0 !== ((int)$ret)) {
            throw new \LogicException("Could not exec command", $ret);
        }
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

        if (null === $maxHeight) {
            $size = ((int)$maxWidth);
        } else if (null === $maxWidth) {
            $size = 'x' . ((int)$maxHeight);
        } else {
            if (!$keepRatio) {
                return $this->doScaleAndCrop($inFile, $outFile, $maxWidth, $maxHeight);
            } else {
                $size = ((int)$maxWidth) . 'x' . ((int)$maxHeight) . '';
            }
        }

        $command = array(
            escapeshellcmd("convert"),
            escapeshellarg($inFile),
            "-auto-orient",
            "-resize",
            "'" . $size . "'",
            escapeshellarg($outFile),
        );

        $ret = 0;
        system(implode(" ", $command), $ret);

        if (0 !== ((int)$ret)) {
            throw new \LogicException("Could not exec command", $ret);
        }
    }
}
