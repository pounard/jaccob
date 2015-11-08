<?php

namespace Wps\View\Helper\Template;

namespace Jaccob\MediaBundle\Twig;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Util\FileSystem;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the thumbnail_grid() function that builds a nice media grid by
 * computing various image heights and arranging them into columns 
 */
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
            new \Twig_SimpleFunction('media_grid', [$this, 'createGrid'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
            new \Twig_SimpleFunction('media_thumbnail', [$this, 'createThumbnail'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
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
     * @param boolean $lazy
     *   Lazy load images.
     *
     * @return string
     */
    public function createThumbnail(\Twig_Environment $twig, Media $media, $size = 100, $withLink = false, $lazy = true)
    {
        // Better be safe than sorry.
        if (!$media->physical_path) {
            return '';
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
            if (is_string($withLink)) {
                // @todo URL with base path
                $href = '/' . $withLink;
            } else {
                // @todo URL with base path
                $href = '/' . FileSystem::pathJoin('media/view', $media->id);
            }
        }

        ///$imgTag = '<img class="lazy-load" data-src="' . $src . '" alt="' . $media->getDisplayName() . '" width="' . $width . '" height="' . $height . '"/>';
        $imgTag = '<img src="' . $src . '" alt="' . $media->user_name . '" width="' . $width . '" height="' . $height . '"/>';

        if ($href) {
            return '<a href="' . $href . '" title="View larger">' . $imgTag . '</a>';
        } else {
            return $imgTag;
        }
    }

    /**
     * Generate thumbnail grid
     *
     * @param \Jaccob\MediaBundle\Model\Media[] $mediaList
     * @param int $columns
     * @param int $width
     * @param string $withLink
     * @param int|string $toSize
     *
     * @return string
     */
    public function createGrid(\Twig_Environment $twig, $mediaList, $columns = 3, $width = 240, $withLink = false)
    {
        $columnsData = array_fill(0, $columns, []);
        $columnsSize = array_fill(0, $columns, 0);

        $size = $width;
        if (!is_int($width[0])) {
            $width = (int)substr($width, 1);
        }

        foreach ($mediaList as $media) {

            // Better be safe than sorry.
            if (!$media instanceof Media) {
                continue; 
            }
            if (!$media->physical_path) {
                continue;
            }
            if (!$media->width || !$media->height) {
                continue;
            }

            $currentColumn = 0;
            $currentSize = null;

            for ($i = 0; $i < $columns; ++$i) {
                if (null === $currentSize || $columnsSize[$i] < $currentSize) {
                    $currentColumn = $i;
                    $currentSize = $columnsSize[$i];
                }
            }

            $height = floor(($width / $media->getWidth()) * $media->getHeight());

            $columnsSize[$currentColumn] += $height;
            $columnsData[$currentColumn][] = $media;
        }

        return $twig->render('JaccobMediaBundle:Helper:thumbnailGrid.html.twig', [
            'columns'   => $columnsData,
            'width'     => $size,
            'withLink'  => $withLink,
        ]);
    }
}
