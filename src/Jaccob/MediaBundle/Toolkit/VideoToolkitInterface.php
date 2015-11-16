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

    /**
     * Transcode video
     *
     * @param string $inFile
     * @param string $outFile
     * @param string $video
     * @param string $format
     * @param string $audio
     * @param array $options
     */
    public function transcode($inFile, $outFile, $video, $format, $audio = null, $options = []);
}
