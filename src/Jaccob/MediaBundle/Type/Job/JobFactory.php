<?php

namespace Jaccob\MediaBundle\Type\Job;

class JobFactory
{
    /**
     * @var \Jaccob\MediaBundle\Type\Job\JobInterface[]
     */
    protected $registeredJobs = [];

    /**
     * Register a type
     *
     * @param string $type
     * @param \Jaccob\MediaBundle\Type\Job\JobInterface $instance
     */
    public function addJob($type, $instance)
    {
        $this->registeredJobs[$type] = $instance;
    }

    /**
     * Does given type exists and is valid
     *
     * @param string $type
     * @param boolean $throwException
     *
     * @return boolean
     */
    public function isTypeValid($type, $throwException = false)
    {
        if (!isset($this->registeredJobs[$type])) {
            if ($throwException) {
                throw new \InvalidArgumentException(sprintf("'%s' job type does not exist", $type));
            }
            return false;
        }
        return true;
    }

    /**
     * Create an instance of the given job type
     *
     * @param string $type
     *
     * @return \Jaccob\MediaBundle\Type\Job\JobInterface
     */
    public function createJob($type)
    {
        $this->isTypeValid($type, true);

        return $this->registeredJobs[$type];
    }
}
