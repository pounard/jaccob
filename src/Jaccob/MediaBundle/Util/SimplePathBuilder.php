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
        $filename = $media->name;

        if ($pos = strrpos($filename, '.')) {
            $filename = substr($filename, 0, $pos);
        }

        return $album->ts_added->format('Y/m/') . $album->id . '/' . $filename;
    }
}