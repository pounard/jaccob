<?php

namespace Jaccob\MediaBundle\Type;

use Jaccob\MediaBundle\Type\TypeFinderService;

trait TypeFinderAwareTrait
{
    /**
     * @var \Jaccob\MediaBundle\Type\TypeFinderService
     */
    protected $typeHelper;

    /**
     * Set media helper
     *
     * @param \Jaccob\MediaBundle\Type\TypeFinderService $typeFinder
     */
    public function setTypeFinder(TypeFinderService $typeFinder)
    {
        $this->typeHelper = $typeFinder;
    }
}
