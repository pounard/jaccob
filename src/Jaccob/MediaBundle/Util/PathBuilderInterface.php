<?php

namespace Jaccob\MediaBundle\Util;

use Jaccob\MediaBundle\Model\Album;
use Jaccob\MediaBundle\Model\Media;

interface PathBuilderInterface
{
    /**
     * Build the media file system path
     *
     * @param \Jaccob\MediaBundle\Model\Album $album
     *   Incomplete album being built
     * @param \Jaccob\MediaBundle\Model\Media $media
     *   Incomplete media being built
     *
     * @return string
     *   Media physical path, without the file extension
     */
    public function buildPath(Album $album, Media $media);
}
