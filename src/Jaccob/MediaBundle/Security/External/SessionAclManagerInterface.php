<?php

namespace Jaccob\MediaBundle\Security\External;

/**
 * External session ACL manager has the only purpose of allowing the HTTPd
 * to run an external component to determine the access rights on images that
 * it'll serve.
 *
 * The only information the HTTPd will know is the media URL and the session
 * cookie, so will only know the external session manager.
 *
 * This information by design will be higly mutable, and potentially written
 * once per HTTP hit on the server, and read for each media URL asked to the
 * HTTPd, so the backend needs to be extremely fast on read, and lightening
 * fast on writes.
 *
 * So what happens, everytime that the album voter will be triggered, an
 * AlbumAuthEvent will be invoked, the ExternalSesssionAclListener, if set
 * will react and call the add or remove method of this object.
 *
 * That's pretty much it for our side, but in the HTTPd side, an external
 * component supposed to be able to talk to the configured implementation will
 * ask the database for the authorization using the session cookie.
 *
 * Please note that outside of the AlbumVoter there is various calls to the
 * event invokation, for exemple in the album controller in order to display
 * the album list, this is necessary.
 *
 * There is another important point to consider is the fact that the delete
 * calls won't probably be done, so your backend has the responsibility to drop
 * expired entries whenever it can.
 *
 * @see
 *  \Jaccob\MediaBundle\Controller\AlbumController
 *  \Jaccob\MediaBundle\Event\AlbumAuthEvent
 *  \Jaccob\MediaBundle\EventListener\ExternalSessionAclListener
 *  \Jaccob\MediaBundle\Security\Authorization\Voter\AlbumVoter
 */
interface SessionAclManagerInterface
{
    /**
     * Default ACL lifetime, one hour will be more than enough
     */
    const DEFAULT_LIFETIME = 3600;

    /**
     * Add authorization for the album
     *
     * @param string $sessionId
     * @param int[] $albumIdList
     * @param int $lifetime
     */
    public function addAlbumAuthorization($sessionId, $albumIdList, $lifetime = self::DEFAULT_LIFETIME);

    /**
     * Remove authorization for the album
     *
     * @param string $sessionId
     * @param int[] $albumId
     */
    public function removeAlbumAuthorization($sessionId, $albumIdList);

    /**
     * Delete for given session
     *
     * @param string $sessionId
     */
    public function deleteForSession($sessionId);

    /**
     * Delete all expired entries
     */
    public function deleteExpired();

    /**
     * Delete all entries
     */
    public function deleteAll();
}
