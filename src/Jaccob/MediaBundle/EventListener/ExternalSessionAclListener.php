<?php

namespace Jaccob\MediaBundle\EventListener;

use Jaccob\MediaBundle\Event\AlbumAuthEvent;
use Jaccob\MediaBundle\Security\External\SessionAclManagerInterface;

/**
 * @see \Jaccob\MediaBundle\Security\External\SessionAclManagerInterface
 */
class ExternalSessionAclListener
{
    /**
     * @var \Jaccob\MediaBundle\Security\External\SessionAclManagerInterface
     */
    protected $aclManager;

    public function setAclManager(SessionAclManagerInterface $aclManager)
    {
        $this->aclManager = $aclManager;
    }

    public function onAuthorization(AlbumAuthEvent $event)
    {
        if ($event->isAuthorized()) {
            $this->aclManager->addAlbumAuthorization($event->getSessionId(), $event->getAlbumIdList());
        } else {
            $this->aclManager->removeAlbumAuthorization($event->getSessionId(), $event->getAlbumIdList());
        }
    }
}
