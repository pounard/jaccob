<?php

namespace Jaccob\MediaBundle\Twig;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Util\MediaHelperAwareTrait;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MediaTypeExtension extends \Twig_Extension implements ContainerAwareInterface
{
    use MediaHelperAwareTrait;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
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
            new \Twig_SimpleFunction('media_full', [$this, 'createMediaFull'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
            new \Twig_SimpleFunction('media_thumbnail', [$this, 'createMediaThumbnail'], [
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
     * Build variables for template
     *
     * @param Media $media
     * @param int $defaultSize
     * @param string $modifier
     *
     * @return mixed[]
     */
    protected function getVariables(Media $media, $defaultSize = null, $modifier = null, $includeFull = false)
    {
        $allowedSizes = $this->container->getParameter('jaccob_media.size.list');
        $allowedSizes = array_filter($allowedSizes, 'is_numeric');
        sort($allowedSizes);

        if ($defaultSize && !in_array($defaultSize, $allowedSizes)) {
            $defaultSize = null;
        }

        if (!$defaultSize) {
            // Arbitrary take the smallest one and potentially save some
            // bandwidth for older or outdated devices
            $defaultSize = max($allowedSizes);
        }

        if ($includeFull) {
            $allowedSizes[] = 'full';
        }

        $width  = $media->width;
        $height = $media->height;

        $ret = ['derivatives' => []];

        foreach ($allowedSizes as $size) {

            $rel = $this->mediaHelper->getThumbnailURI($media, $size, $modifier);
            if (!$rel) {
                continue;
            }

            // Default modifier is width, seems logic at this point
            switch ($modifier) {

                case 's':
                    $width  = $size;
                    $height = $size;
                    break;

                case 'h':
                    $height = $size;
                    $width  = '';
                    break;

                default:
                case 'w':
                    $width  = $size;
                    $height = '';
                    $modifier = 'w';
                    break;
            }

            $derivative = [
                // @todo Use symfony path generator
                'href'      => '/' . $rel,
                'width'     => $width,
                'height'    => $height,
                'size'      => $size,
                'modifier'  => $modifier,
                'mimetype'  => $media->mimetype,
            ];

            $ret['derivatives'][] = $derivative;

            if ($defaultSize === $size) {
                $ret['default'] = $derivative;
            }
        }

        $ret['media'] = $media;

        return $ret;
    }

    /**
     * Generate a responsive version of the image, including all configured
     * sizes
     *
     * @param \Twig_Environment $twig
     * @param Media $media
     * @param int $defaultSize
     * @param string $modifier
     *
     * @return string
     */
    public function createMediaFull(\Twig_Environment $twig, Media $media, $defaultSize = null, $modifier = null)
    {
        $templateName = $this
            ->mediaHelper
            ->getType($media)
            ->getTwigTemplateName()
        ;

        if (!$templateName) {
            $templateName = 'JaccobMediaBundle:Type:default.html.twig';
        }

        $variables = $this->getVariables($media, $defaultSize, $modifier, true);
        $variables += [
            'full'      => true,
            'thumbnail' => false,
            'viewport'  => 100,
        ];

        // @todo Needs something better than this
        if (false !== strpos($media->mimetype, 'video')) {
            $variables['derivatives'] = [];
            $derivatives = $this->mediaHelper->getMediaDerivativeModel()->findByMedia($media->id);
            if ($derivatives) {
                foreach ($derivatives as $derivative) {
                    $variables['derivatives'][] = [
                        'href'      => '/' . $this->mediaHelper->getDerivativeURI($derivative),
                        'width'     => $derivative->width,
                        'height'    => $derivative->height,
                        'size'      => 'full',
                        'modifier'  => null,
                        'mimetype'  => $derivative->mimetype,
                    ];
                }
            }
        }

        return $twig->render($templateName, $variables);
    }

    /**
     * Generate media thumbnail
     *
     * @param Media $media
     * @param int $defaultSize
     * @param string $modifier
     * @param boolean $includeFull
     *
     * @return string
     */
    public function createMediaThumbnail(\Twig_Environment $twig, Media $media, $defaultSize = null, $modifier = null, $includeFull = false)
    {
        $templateName = $this
            ->mediaHelper
            ->getType($media)
            ->getTwigTemplateName()
        ;

        if (!$templateName) {
            $templateName = 'JaccobMediaBundle:Type:default.html.twig';
        }

        $variables = $this->getVariables($media, $defaultSize, $modifier, false);
        $variables += [
            'full'      => false,
            'thumbnail' => true,
            'viewport'  => 20,
        ];

        return $twig->render($templateName, $variables);
    }
}
