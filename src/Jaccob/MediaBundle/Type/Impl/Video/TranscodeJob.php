<?php

namespace Jaccob\MediaBundle\Type\Impl\Video;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Type\Job\JobInterface;

class TranscodeJob implements JobInterface
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
