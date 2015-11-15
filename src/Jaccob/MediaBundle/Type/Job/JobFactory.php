<?php

namespace Jaccob\MediaBundle\Type\Job;

class JobFactory
{
    /**
     * @var string[]
     */
    protected $registeredTypes = [];

    /**
     * Register a type
     *
     * @param string $type
     * @param string $class
     */
    public function addType($type, $class)
    {
        $this->registeredTypes[$type] = $class;
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
        if (!isset($this->registeredTypes[$type])) {
            if ($throwException) {
                throw new \InvalidArgumentException(sprintf("'%s' job type does not exist", $type));
            }
            return false;
        }

        $class = $this->registeredTypes[$type];
        if (!class_exists($class)) {
            if ($throwException) {
                throw new \InvalidArgumentException(sprintf("'%s' class does not exist", $type));
            }
            return false;
        }

        if (!is_subclass_of($class, '\Jaccob\MediaBundle\Type\Job')) {
            if ($throwException) {
                throw new \InvalidArgumentException(sprintf("'%s' class does not implements \Jaccob\MediaBundle\Type\JobQueue\JobInterface", $type));
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

        return $this->registeredTypes[$type]();
    }
}
