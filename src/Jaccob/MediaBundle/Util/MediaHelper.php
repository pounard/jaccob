<?php

namespace Jaccob\MediaBundle\Util;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Type\TypeFinderService;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Sorry for the name
 */
class MediaHelper extends ContainerAware
{
    /**
     * @var \Jaccob\MediaBundle\Type\TypeFinderService
     */
    protected $typeFinder;

    /**
     * Set type finder
     *
     * @param TypeFinderService $typeFinder
     */
    public function setTypeFinder(TypeFinderService $typeFinder)
    {
        $this->typeFinder = $typeFinder;
    }

    /**
     * Get original file path, suitable for URLs
     *
     * @param Media $media
     *
     * @return string
     */
    public function getOriginalPath(Media $media)
    {
        $publicDirectory = $this->container->getParameter('jaccob_media.directory.public');

        return FileSystem::pathJoin($publicDirectory, 'full', $media->physical_path);
    }

    /**
     * Get specific thumbnail path, suitable for URLs
     *
     * @param Media $media
     * @param int $size
     * @param string $modifier
     *
     * @return string
     */
    public function getThumbnailPath(Media $media, $size, $modifier = null)
    {
        $type = $this->typeFinder->getTypeFor($media->mimetype);

        if (!$type->canDoThumbnail()) {
            return;
        }

        $publicDirectory = $this->container->getParameter('jaccob_media.directory.public');

        if ($modifier) {
            $size = $modifier . $size;
        }

        $ext = $type->getThumbnailExtension($media, $size, $modifier);

        return FileSystem::pathJoin($publicDirectory, $size, $media->getPhysicalPathWithoutExtension() . '.' . $ext);
    }

    /**
     * Create thumbnail and return the real URL
     *
     * @param Media $media
     * @param int $size
     * @param string $modifier
     *
     * @return string
     *   Thumbnail path relative to public folder or false if failed
     */
    public function createThumbnail(Media $media, $size, $modifier = null)
    {
        $inFile = $this->getOriginalPath($media);
        $outFile = $this->getThumbnailPath($media, $size, $modifier);

        if (!$outFile) {
            return false;
        }

        $type = $this->typeFinder->getTypeFor($media->mimetype);
        if (!$type->createThumbnail($media, $inFile, $outFile, $size, $modifier)) {
            return false;
        }

        return $outFile;
    }
}
