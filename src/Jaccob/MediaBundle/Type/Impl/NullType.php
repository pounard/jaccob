<?php

namespace Jaccob\MediaBundle\Type\Impl;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Type\TypeInterface;

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

    /**
     * {@inheritdoc}
     */
    public function canDoThumbnail()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function createThumbnail(Media $media, $inFile, $outFile, $size, $modifier)
    {
        return false;
    }
}
