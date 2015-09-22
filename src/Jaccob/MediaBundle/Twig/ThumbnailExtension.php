<?php

namespace Jaccob\MediaBundle\Twig;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Util\FileSystem;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ThumbnailExtension extends \Twig_Extension implements ContainerAwareInterface
{
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('thumbnail', [$this, 'createThumbnail']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jaccob_media_thumbnail';
    }

    /**
     * Generate media thumbnail
     *
     * @param Media $media
     *   The media
     * @param int $size
     *   Size in pixels
     * @param boolean|string $withLink
     *   True and link will be generated automatically, a string and it will
     *   be used as the link
     * @param int $toSize
     *   Size the image should be displayed with when fullscreen, only used
     *   when you set true to the $withLink parameter
     *
     * @return string
     */
    public function createThumbnail(Media $media, $size = 100, $withLink = true, $toSize = 600)
    {
        if (!$media->physical_path) {
            return ''; // Never crash on display
        }

        if (!$media->width || !$media->height) {
            return '';
        }

        if (is_string($size) && !is_numeric($size[0])) {
            if ('h' === $size[0]) {
                $height = (int)substr($size, 1);
                $width = floor(($height / $media->height) * $media->width);
            } else if ('w' === $size[0]) {
                $width = (int)substr($size, 1);
                $height = floor(($width / $media->width) * $media->height);
            } else if ('s' === $size[0]) {
                $width = $height = (int)substr($size, 1);
                $size = $width;
            } else {
                // Dafuck?
                return '';
            }
        } else {
            $width = (int)$size;
            $height = floor(($width / $media->width) * $media->height);
            $size = 'w' . $size;
        }

        $publicDirectory = $this->container->getParameter('jaccob_media.directory.relative');
        // @todo URL with base path
        $src = '/' . FileSystem::pathJoin($publicDirectory, $size, $media->physical_path);

        $href   = null;
        if ($withLink) {
            if ('full' === $toSize) {
                $href = $this->url(FileSystem::pathJoin($publicDirectory, $toSize, $media->physical_path));
            } else if (is_string($withLink)) {
                // @todo URL with base path
                $href = '/' . $withLink;
            } else {
                // @todo URL with base path
                $href = '/' . FileSystem::pathJoin('media', $media->id, $toSize);
            }
        }

        $imgTag = '<img class="lazy-load" data-src="' . $src . '" alt="' . $media->user_name . '" width="' . $width . '" height="' . $height . '"/>';

        if ($href) {
            return '<a href="' . $href . '" title="View larger">' . $imgTag . '</a>';
        } else {
            return $imgTag;
        }
    
    }
}
