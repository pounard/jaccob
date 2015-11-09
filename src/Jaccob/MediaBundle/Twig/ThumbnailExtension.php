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
            new \Twig_SimpleFunction('responsive_image', [$this, 'responsiveImage'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
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
     * Generate a responsive version of the image, including all configured
     * sizes
     *
     * @param \Twig_Environment $twig
     * @param Media $media
     * @param int $defaultSize
     *
     * @return string
     */
    public function responsiveImage(\Twig_Environment $twig, Media $media, $defaultSize = null)
    {
        $allowedSizes = $this->container->getParameter('jaccob_media.size.list');
        $publicDirectory = $this->container->getParameter('jaccob_media.directory.relative');

        $maxWidth = $media->width;

        $sources = [];

        if (!$defaultSize) {
            $defaultSize = reset($allowedSizes);
        }

        /*
         * We are going to use a polyfill, hence the srcset instead of the src
         * attribute within the img tag fallback.
         *
         * This page explains it quite well:
         *   http://www.smashingmagazine.com/2014/05/picturefill-2-0-responsive-images-and-the-perfect-polyfill/
         *
         * Great thanks and all credit to its author.
         *
             <picture>
               <source srcset="extralarge.jpg, extralarge.jpg 2x" media="(min-width: 1000px)">
               <source srcset="large.jpg, large.jpg 2x" media="(min-width: 800px)">
               <source srcset="medium.jpg">
               <img srcset="medium.jpg" alt="Cambodia Map">
             </picture>
         */

        foreach ($allowedSizes as $size) {
            if (is_numeric($size) && $size < $maxWidth) {
                $src = '/' . FileSystem::pathJoin($publicDirectory, 'w' . $size, $media->physical_path);
                $medias = 'min-width: ' . $size . 'px';
                $sources[] = '<source srcset="' . $src . '" media="' . $medias . '"/>';
            } else if ('full' === $size) {
                $src = '/' . FileSystem::pathJoin($publicDirectory, 'full', $media->physical_path);
                $medias = 'min-width: ' . $maxWidth . 'px';
                $sources[] = '<source srcset="' . $src . '" media="' . $medias . '"/>';
            }
        }

        $defaultSrc = '/' . FileSystem::pathJoin($publicDirectory, $defaultSize, $media->physical_path);
        $sources[] = '<img srcset="' . $defaultSrc . '"/>';

        return "\n<picture>\n" . implode("\n", $sources) . "\n</picture>\n";
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
