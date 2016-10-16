<?php

namespace Jaccob\MediaBundle\Type\Impl\Job;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Type\Job\JobInterface;
use Jaccob\MediaBundle\Util\MediaHelperAwareTrait;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Console\Output\OutputInterface;

class MediaThumbnail implements JobInterface
{
    use ContainerAwareTrait;
    use MediaHelperAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function run(Media $media, array $options = [], OutputInterface $output)
    {
        $container = $this->container;
        $allowedSizes = $container->getParameter('jaccob_media.size.list');

        foreach ($allowedSizes as $size) {
            if ('full' !== $size) {
                foreach (['w', 's', 'h'] as $modifier) {

                    $filename = $this->mediaHelper->getThumbnailPath($media, $size, $modifier);
                    if (file_exists($filename)) {
                        // print "Skipped '" . $filename . "'\n";
                        // $output->writeln('<comment>' . sprintf("%s: media skipped", $filename) . '</comment>');
                        continue;
                    }
                    // print "Generating '" . $filename . "'\n";

                    $this->mediaHelper->createThumbnail($media, $size, $modifier);
                }
            }
        }
    }
}
