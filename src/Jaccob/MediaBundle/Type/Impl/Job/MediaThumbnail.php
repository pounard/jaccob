<?php

namespace Jaccob\MediaBundle\Type\Impl\Job;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Type\Job\JobInterface;
use Jaccob\MediaBundle\Util\MediaHelperAwareTrait;

use Symfony\Component\DependencyInjection\ContainerAware;

class MediaThumbnail extends ContainerAware implements JobInterface
{
    use MediaHelperAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function run(Media $media, array $options = [])
    {
        $container = $this->container;
        $allowedSizes = $container->getParameter('jaccob_media.size.list');

        foreach ($allowedSizes as $size) {
            if ('full' !== $size) {
                foreach (['w', 's', 'h'] as $modifier) {

                    $filename = $this->mediaHelper->getThumbnailPath($media, $size, $modifier);
                    if (file_exists($filename)) {
                        print "Skipped '" . $filename . "'\n";
                        continue;
                    }
                    print "Generating '" . $filename . "'\n";

                    $this->mediaHelper->createThumbnail($media, $size, $modifier);
                }
            }
        }
    }
}
