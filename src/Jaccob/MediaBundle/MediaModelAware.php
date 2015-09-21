<?php

namespace Jaccob\MediaBundle;

use PommProject\Foundation\Session;

trait MediaModelAware
{
    /**
     * @var \PommProject\Foundation\Session
     */
    private $pommSession;

    /**
     * Set session
     *
     * @param \PommProject\Foundation\Session $pommSession
     */
    public function setMediaSession(Session $pommSession)
    {
        $this->pommSession = $pommSession;
    }

    /**
     * Get pomm account session
     *
     * @return \PommProject\Foundation\Session
     */
    protected function getMediaSession()
    {
        if ($this->pommSession) {
            return $this->pommSession;
        }

        // When we are working with an object plugged to the DIC.
        return $this->get('pomm')->getSession('default');
    }

    /**
     * Get pomm media model
     *
     * @return \Jaccob\MediaBundle\Model\MediaModel
     */
    protected function getMediaModel()
    {
        return $this
            ->getAccountSession()
            ->getModel('\Jaccob\MediaBundle\Model\MediaModel')
        ;
    }

    /**
     * Get pomm album model
     *
     * @return \Jaccob\MediaBundle\Model\AlbumModel
     */
    protected function getAlbumModel()
    {
        return $this
            ->getAccountSession()
            ->getModel('\Jaccob\MediaBundle\Model\AlbumModel')
        ;
    }

    /**
     * Get pomm device model
     *
     * @return \Jaccob\MediaBundle\Model\DeviceModel
     */
    protected function getDeviceModel()
    {
        return $this
            ->getAccountSession()
            ->getModel('\Jaccob\MediaBundle\Model\DeviceModel')
        ;
    }
}
