<?php

namespace Wps\View\Helper\Template;

namespace Jaccob\MediaBundle\Twig;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Util\MediaHelper;

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
     * @var \Jaccob\MediaBundle\Util\MediaHelper
     */
    protected $mediaHelper;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Set media helper
     *
     * @param \Jaccob\MediaBundle\Util\MediaHelper $mediaHelper
     */
    public function setMediaHelper(MediaHelper $mediaHelper)
    {
        $this->mediaHelper = $mediaHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('media_responsive', [$this, 'createResponsivePicture'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
            new \Twig_SimpleFunction('media_thumbnail', [$this, 'createThumbnailImage'], [
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

    protected function getMediaUrl(Media $media, $size, $modifier = null)
    {
        $allowedSizes = $this->container->getParameter('jaccob_media.size.list');

        if (!in_array($size, $allowedSizes)) {
            // Take the nearest
            $closest = null;
            foreach ($allowedSizes as $item) {
                if ($closest === null || abs($size - $closest) > abs($item - $closest)) {
                    $closest = $item;
                }
            }
        }

        // @todo Use symfony path generator
        return '/' . $this->mediaHelper->getThumbnailURI($media, $size, $modifier);
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
    public function createResponsivePicture(\Twig_Environment $twig, Media $media, $defaultSize = null, $maxSize = null, $modifier = null)
    {
        $allowedSizes = $this->container->getParameter('jaccob_media.size.list');

        $allowedSizes = array_filter($allowedSizes, 'is_numeric');
        sort($allowedSizes);

        // Do not ever try to include sizes over the image size
        if (!$maxSize || $media->width < $maxSize) {
            $maxSize = $media->width;
        }

        if (!$defaultSize) {
            if ($maxSize) {
                // Maximum size if set probably is the target size for normal
                // display
                $defaultSize = $maxSize;
            } else {
                // Arbitrary take the smallest one and potentially save some
                // bandwidth for older or outdated devices
                $defaultSize = max($allowedSizes);
            }
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

        $sources = [];

        // Default modifier is width, seems logic at this point
        switch ($modifier) {
            case 's':
                break;
            case 'h':
                break;
            default:
            case 'w':
                $modifier = 'w';
                break;
        }

        foreach ($allowedSizes as $size) {
            if ($size < $maxSize) {
                $medias = 'max-width: ' . ((int)$size) . 'px';
                $href = $this->getMediaUrl($media, $size, $modifier);
                $sources[] = '<source srcset="' . $href . ' 1x" media="(' . $medias . ')"/>';
            }
        }

        // Full first, for very big sreens.
        if ($media->width < $maxSize) {
            $medias = 'max-width: ' . ((int)$maxSize) . 'px';
            $href = $this->getMediaUrl($media, 'full');
            $sources[] = '<source srcset="' . $href . ' 1x" media="(' . $medias . ')"/>';
        }

        $sources[] = '<img srcset="' . $this->getMediaUrl($media, $defaultSize, $modifier) . ' 1x"/>';

        return "\n<picture>\n" . implode("\n", $sources) . "\n</picture>\n";
    }

    /**
     * Generate media thumbnail
     *
     * @param Media $media
     *   The media
     * @param int $size
     *   Size in pixels
     * @param string $modifier
     *   Modifier:
     *     'w' (default) : size is width (scale)
     *     'h' : size is height (scale)
     *     's' : size is square (scale and crop)
     *
     * @return string
     */
    public function createThumbnailImage(\Twig_Environment $twig, Media $media, $defaultSize = null, $maxSize = null, $modifier = null)
    {
        $allowedSizes = $this->container->getParameter('jaccob_media.size.list');

        $allowedSizes = array_filter($allowedSizes, 'is_numeric');
        sort($allowedSizes);

        // Do not ever try to include sizes over the image size
        if (!$maxSize || $media->width < $maxSize) {
            $maxSize = $media->width;
        }

        if (!$defaultSize) {
            if ($maxSize) {
                // Maximum size if set probably is the target size for normal
                // display
                $defaultSize = $maxSize;
            } else {
                // Arbitrary take the smallest one and potentially save some
                // bandwidth for older or outdated devices
                $defaultSize = max($allowedSizes);
            }
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
            <img src="small.jpg"
                 srcset="large.jpg 1024w,
                         medium.jpg 640w,
                         small.jpg 320w"
                 sizes="(min-width: 36em) 33.3vw,
                        100vw"
                 alt="A rad wolf" />
         */

        $sets = [];

        // Default modifier is width, seems logic at this point
        switch ($modifier) {
            case 's':
                break;
            case 'h':
                break;
            default:
            case 'w':
                $modifier = 'w';
                break;
        }


        foreach ($allowedSizes as $size) {
            if ($size < $maxSize) {
                $href = $this->getMediaUrl($media, $size, $modifier);
                $sets[] = $href . ' ' . $size . 'w';
            }
        }

        if (!$maxSize) {
            $href = $this->getMediaUrl($media, 'full');
            $sets[] = $href . ' ' . $size . 'w';
        }

        $defaultHref = $this->getMediaUrl($media, $defaultSize, $modifier);

        return '<img srcset="' . implode(",\n", $sets) . '" sizes="20vw" src="' . $defaultHref . '" />';
    }
}
