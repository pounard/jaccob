<?php

namespace Jaccob\MediaBundle\Type\Job;

use Jaccob\MediaBundle\Model\Media;

interface JobInterface
{
    /**
     * Run the job
     *
     * @param \Jaccob\MediaBundle\Model\Media $media
     *   Media to operate on
     * @param mixed[] $options
     *   Options given when job was queued at the discretion of the caller
     */
    public function run(Media $media, array $options = []);
}
