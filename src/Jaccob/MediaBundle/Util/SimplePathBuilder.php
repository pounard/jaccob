<?php

namespace Jaccob\MediaBundle\Util;

use Jaccob\MediaBundle\Model\Album;
use Jaccob\MediaBundle\Model\Media;

class SimplePathBuilder implements PathBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildPath(Album $album, Media $media)
    {
        return $album->ts_added->format('Y/m/') . $album->id;
    }
}