<?php

namespace Jaccob\MediaBundle\Util;

/**
 * Sorry for the name
 */
trait MediaHelperAwareTrait
{
    /**
     * @var \Jaccob\MediaBundle\Util\MediaHelper
     */
    protected $mediaHelper;

    /**
     * Set media helper
     *
     * @param \Jaccob\MediaBundle\Util\MediaHelper $mediaHelper
     */
    public function setMediaHelper(MediaHelper $mediaHelper)
    {
        $this->mediaHelper = $mediaHelper;
    }
}
