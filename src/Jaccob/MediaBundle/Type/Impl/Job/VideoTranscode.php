<?php

namespace Jaccob\MediaBundle\Type\Impl\Job;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Type\Job\JobInterface;

use Symfony\Component\DependencyInjection\ContainerAware;

class VideoTranscode extends ContainerAware implements JobInterface
{
    /**
     * {@inheritdoc}
     */
    public function run(Media $media, array $options = [])
    {
        // @todo
        throw new \Exception("Not implemented yet");
    }
}
