<?php

namespace Jaccob\MediaBundle\Security\Authorization\Voter;

use Jaccob\AccountBundle\Security\User\JaccobUser;

use Jaccob\MediaBundle\Event\AlbumAuthEvent;
use Jaccob\MediaBundle\Model\Album;
use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AlbumVoter extends Voter
{
    use MediaModelAware;
    use ContainerAwareTrait;

    /**
     * View the album or its medias
     */
    const VIEW = 'view';

    /**
     * Edit the album
     */
    const EDIT = 'edit';

    /**
     * Edit share settings
     */
    const SHARE = 'share';

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * Set session
     *
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Album && in_array($attribute, [self::VIEW, self::EDIT, self::SHARE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        $isAnonymous = is_string($user);

        if (!$isAnonymous && !$user instanceof JaccobUser) {
            return false;
        }
        if (!$subject instanceof Album) {
            return false;
        }
        $album = $subject; // Useless.

        $isAuthorized = false;

        switch ($attribute) {

            case self::VIEW:

                // Either the user is owner, or user
                if (!$isAnonymous && $user->getAccount()->getId() === $album->id_account) {
                    $isAuthorized = true;
                } else {
                    // Check in session
                    $isAuthorized = $this
                        ->getAlbumModel()
                        ->isAlbumInSession(
                            $album->id,
                            $this->session->getId()
                        )
                    ;
                }

                $this->container->get('event_dispatcher')->dispatch(
                    AlbumAuthEvent::AUTH,
                    new AlbumAuthEvent([$album->id], $this->session->getId(), $isAuthorized)
                );
                break;

            case self::EDIT:
            case self::SHARE:
                // Only owner can edit his own album for now
                $isAuthorized = !$isAnonymous && $user->getAccount()->getId() === $album->id_account;
                break;
        }

        return $isAuthorized;
    }
}
