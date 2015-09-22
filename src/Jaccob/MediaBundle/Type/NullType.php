<?php

namespace Jaccob\MediaBundle\Type;

use Jaccob\MediaBundle\Model\Media;

/**
 * Null implementation for unknown types
 */
class NullType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function findMetadata(Media $media, $filename = null)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return false;
    }
}
