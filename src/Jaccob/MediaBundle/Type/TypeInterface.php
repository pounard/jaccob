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
}
