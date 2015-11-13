<?php

namespace Jaccob\MediaBundle\Util;

trait PathBuilderAwareTrait {

    /**
     * @var \Jaccob\MediaBundle\Util\PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * Set path builder
     *
     * @param \Jaccob\MediaBundle\Util\PathBuilderInterface $pathBuilder
     */
    public function setPathBuilder(PathBuilderInterface $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }
}
