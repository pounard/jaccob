<?php

namespace Jaccob\MediaBundle\Type\Job;

use Jaccob\MediaBundle\Model\Media;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Please consider carefully that all jobs will be services instance, and by
 * so must remain stateless
 */
interface JobInterface
{
    /**
     * Run the job
     *
     * @param \Jaccob\MediaBundle\Model\Media $media
     *   Media to operate on
     * @param mixed[] $options
     *   Options given when job was queued at the discretion of the caller
     * @param OutputInterface $output
     */
    public function run(Media $media, array $options = [], OutputInterface $output);
}
