<?php

namespace Jaccob\MediaBundle\Type\Impl;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Type\TypeInterface;

abstract class AbstractType implements TypeInterface
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
        return true;
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
    public function getThumbnailExtension(Media $media, $size, $modifier)
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
