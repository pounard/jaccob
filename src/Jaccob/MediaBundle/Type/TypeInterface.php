<?php

namespace Jaccob\MediaBundle\Type;

use Jaccob\MediaBundle\Model\Media;

/**
 * Represents a mime type handler and includes the logic on how to operate
 * with it, such as extracting metadata and creating thumbnails
 */
interface TypeInterface
{
    /**
     * Find media metadata
     *
     * @param Media $media
     * @param string $filename
     *   When working with a temporary path, during upload for example, pass
     *   here the real file path in order for the type handler to be able to
     *   access it
     *
     * @return array
     *   Key value pairs where keys are attribute names and values are arrays
     *   of values which means that properties can be multivalued; Each value
     *   can be any scalar type
     */
    public function findMetadata(Media $media, $filename = null);

    /**
     * Tell if the software can process this type
     *
     * @return boolean
     */
    public function isValid();

    /**
     * Is this type able to generate thumbnails for files
     *
     * @return boolean
     */
    public function canDoThumbnail();

    /**
     * This sounds dumb, but the generated thumbnail extension might change
     * depending on the data type
     *
     * @param \Jaccob\MediaBundle\Model\Media $media
     *   Media to generate the thumbnail for
     * @param int $size
     *   Size in pixels (width, height, or both, see the $modifier parameter)
     * @param string $modifier
     *   's', 'w' or 'h'
     *
     * @return string
     */
    public function getThumbnailExtension(Media $media, $size, $modifier);

    /**
     * Generate thumbnail
     *
     * @param \Jaccob\MediaBundle\Model\Media $media
     *   Media to generate the thumbnail for
     * @param string $inFile
     *   Input file real path
     * @param string $outFile
     *   Output file real path
     * @param int $size
     *   Size in pixels (width, height, or both, see the $modifier parameter)
     * @param string $modifier
     *   's', 'w' or 'h'
     *
     * @return boolean
     */
    public function createThumbnail(Media $media, $inFile, $outFile, $size, $modifier);
}
