<?php

namespace Jaccob\MediaBundle;

use PommProject\Foundation\Session;

use Symfony\Component\DependencyInjection\ContainerAware;

trait MediaModelAware
{
    /**
     * @var \PommProject\Foundation\Session
     */
    private $accountPommSession;

    /**
     * Set session
     *
     * @param \PommProject\Foundation\Session $pommSession
     */
    public function setMediaSession(Session $pommSession)
    {
        $this->accountPommSession = $pommSession;
    }

    /**
     * Get pomm account session
     *
     * @return \PommProject\Foundation\Session
     */
    protected function getMediaSession()
    {
        if ($this->accountPommSession) {
            return $this->accountPommSession;
        }

        // When we are working with an object plugged to the DIC.
        if ($this instanceof ContainerAware) {
            return $this->container->get('pomm')->getSession('default');
        }

        // When we are working with a controller.
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
            ->getMediaSession()
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
            ->getMediaSession()
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
            ->getMediaSession()
            ->getModel('\Jaccob\MediaBundle\Model\DeviceModel')
        ;
    }

    /**
     * Get task or throw a 404 or 403 error depending on data
     *
     * @param int $albumId
     *   Album identifier
     *
     * @return \Jaccob\MediaBundle\Model\Album
     */
    protected function findAlbumOr404($id)
    {
        /* @var $task \Jaccob\MediaBundle\Model\Album */
        $album = $this->getAlbumModel()->findByPK(['id' => $id]);

        if (!$album) {
            throw $this->createNotFoundException(sprintf(
                "Album with id '%d' does not exists",
                $id
            ));
        }

        return $album;
    }
}
