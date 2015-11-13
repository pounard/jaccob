<?php

namespace Jaccob\MediaBundle\Toolkit;

interface VideoToolkitInterface
{
    /**
     * Get video original dimensions
     *
     * @param string $inFile
     *
     * @return int[]
     *   First value is width, second is height
     */
    public function getDimensions($inFile);

    /**
     * Generate video thumbnail
     *
     * Thumbnail size will be the original video size
     *
     * @param string $inFile
     * @param string $outFile
     */
    public function generateThumbnail($inFile, $outFile);

    /**
     * Find video metadata
     *
     * @param string $inFile
     */
    public function findMetaData($inFile);
}
