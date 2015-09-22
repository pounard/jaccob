<?php

namespace Jaccob\MediaBundle\Toolkit;

interface ImageToolkitInterface
{
    /**
     * Scale and crop image
     *
     * @param string $inFile
     * @param string $outFile
     * @param int $width
     * @param int $height
     */
    public function scaleAndCrop($inFile, $outFile, $width, $height);

    /**
     * Scale image
     *
     * @param string $inFile
     * @param string $outFile
     * @param int $maxWidth
     * @param int $maxHeight
     */
    public function scaleTo($inFile, $outFile, $maxWidth = null, $maxHeight = null, $keepRatio = true);
}
