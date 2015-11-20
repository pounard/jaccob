<?php

namespace Jaccob\MediaBundle\Event;

use Jaccob\MediaBundle\Model\Album;

use Symfony\Component\EventDispatcher\Event;

/**
 * @see \Jaccob\MediaBundle\Security\External\SessionAclManagerInterface
 */
class AlbumAuthEvent extends Event
{
    const AUTH = 'jaccob_media.album.auth';

    /**
     * @var int[]
     */
    protected $albumIdList;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var boolean
     */
    protected $isAuthorized;

    /**
     * Default constructor
     *
     * @param int|int[] $albumIdList
     * @param string $sessionId
     * @param boolean
     */
    public function __construct($albumIdList, $sessionId, $isAuthorized)
    {
        $this->albumIdList = is_array($albumIdList) ? $albumIdList : [$albumIdList];
        $this->sessionId = $sessionId;
        $this->isAuthorized = $isAuthorized;
    }

    /**
     * Get album identifiers list
     *
     * @return int[]
     */
    public function getAlbumIdList()
    {
        return $this->albumIdList;
    }

    /**
     * Get session
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Is authorized to see album
     *
     * @return boolean
     */
    public function isAuthorized()
    {
        return $this->isAuthorized;
    }
}
