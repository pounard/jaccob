<?php

namespace Jaccob\MediaBundle\Type\Job;

trait JobFactoryAwareTrait
{
    /**
     * @var \Jaccob\MediaBundle\Type\Job\JobFactory
     */
    protected $jobFactory;

    /**
     * Set job factory
     *
     * @param \Jaccob\MediaBundle\Type\TypeFinderService $typeFinder
     */
    public function setJobFactory(JobFactory $jobFactory)
    {
        $this->jobFactory = $jobFactory;
    }
}
