<?php

namespace Jaccob\MediaBundle\Util;

use Jaccob\AccountBundle\Security\Crypt;

use Jaccob\MediaBundle\Model\Album;
use Jaccob\MediaBundle\Model\Media;

class ObscurePathBuilder implements PathBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildPath(Album $album, Media $media)
    {
        $path = Crypt::getSimpleHash($media->path, Crypt::createSalt());

        return trim(preg_replace('/[^a-zA-Z0-9]{1,}/', '/', $path), "/");
    }
}