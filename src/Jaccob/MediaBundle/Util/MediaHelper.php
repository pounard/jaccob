<?php

namespace Jaccob\MediaBundle\Util;

use Jaccob\MediaBundle\MediaModelAware;
use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Model\MediaDerivative;
use Jaccob\MediaBundle\Type\TypeFinderService;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Sorry for the name
 */
class MediaHelper
{
    use ContainerAwareTrait;
    use MediaModelAware;

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
     * get type for media
     *
     * @param Media $media
     *
     * @return \Jaccob\MediaBundle\Type\TypeInterface
     */
    public function getType(Media $media)
    {
        return $this->typeFinder->getTypeFor($media->mimetype);
    }

    /**
     * Get original absolute file path
     *
     * @param Media $media
     *
     * @return string
     */
    public function getOriginalPath(Media $media)
    {
        $publicDirectory = $this->container->getParameter('jaccob_media.directory.public');

        return FileSystem::pathJoin($publicDirectory, 'full', $media->physical_path, $media->name);
    }

    /**
     * Get specific thumbnail absolute path
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

        if ('full' !== $size) {
            if (!$type->canDoThumbnail()) {
                return;
            }
            if ($modifier) {
                $size = $modifier . $size;
            }
            $ext  = $type->getThumbnailExtension($media, $size, $modifier);
            $name = $media->getNameWithoutExtension() . '.' . $ext;
        } else {
            $name = $media->name;
        }

        $publicDirectory = $this->container->getParameter('jaccob_media.directory.public');

        return FileSystem::pathJoin($publicDirectory, $size, $media->physical_path, $name);
    }

    /**
     * Get specific thumbnail URI, suitable for URLs
     *
     * @param Media $media
     * @param int $size
     * @param string $modifier
     * @param boolean $escape
     *
     * @return string
     */
    public function getMediaURI(Media $media, $size = 'full', $modifier = null, $escape = true)
    {
        $type = $this->typeFinder->getTypeFor($media->mimetype);

        if ('full' !== $size) {
            if (!$type->canDoThumbnail()) {
                return;
            }
            if ($modifier) {
                $size = $modifier . $size;
            }
            $ext  = $type->getThumbnailExtension($media, $size, $modifier);
            $name = $media->getNameWithoutExtension() . '.' . $ext;
        } else {
            $name = $media->name;
        }

        $relativeDirectory = $this->container->getParameter('jaccob_media.directory.relative');

        $ext  = $type->getThumbnailExtension($media, $size, $modifier);
        $path = $media->physical_path;

        if ($escape) {
            $name = rawurldecode($name);
            $path = explode('/', $path);
            array_walk($path, function (&$value) {
                $value = rawurlencode($value);
            });
            $path = implode('/', $path);
        }

        return FileSystem::pathJoin($relativeDirectory, $size, $path, $name);
    }

    /**
     * Get specific derivative thumbnail URI, suitable for URLs
     *
     * Derivatives are not meant to be thumbnailed so this is the only helper
     * you'll get for those
     *
     * @param MediaDerivative $media
     * @param int $size
     * @param string $modifier
     * @param boolean $escape
     */
    public function getDerivativeURI(MediaDerivative $derivative, $escape = true)
    {
          $relativeDirectory = $this->container->getParameter('jaccob_media.directory.relative');

          $path = $derivative->physical_path;
          $name = $derivative->name;

          if ($escape) {
              $name = rawurlencode($name);
              $path = explode('/', $path);
              array_walk($path, function (&$value) {
                  $value = rawurlencode($value);
              });
              $path = implode('/', $path);
          }

          return FileSystem::pathJoin($relativeDirectory, 'full', $path, $name);
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
        $inFile   = $this->getOriginalPath($media);
        $outFile  = $this->getThumbnailPath($media, $size, $modifier);

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
