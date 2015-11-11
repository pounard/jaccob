<?php

namespace Jaccob\MediaBundle\Toolkit;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Uses the convert system command
 */
class ExternalImagickImageToolkit extends AbstractImageToolkit
{
    public function doScaleAndCrop($inFile, $outFile, $width, $height)
    {
        $size = ((int)$width) . "x" . ((int)$height);

        (new ProcessBuilder())
            ->setPrefix("convert")
            ->setArguments([
                $inFile,
                "-auto-orient",
                "-resize",
                $size . "^",
                "-gravity",
                "center",
                "-crop",
                $size . "+0+0",
                $outFile,
            ])
            ->getProcess()
            ->mustRun()
        ;
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

        (new ProcessBuilder())
            ->setPrefix("convert")
            ->setArguments([
                $inFile,
                "-auto-orient",
                "-resize",
                $size,
                $outFile,
            ])
            ->getProcess()
            ->mustRun()
        ;
    }
}
