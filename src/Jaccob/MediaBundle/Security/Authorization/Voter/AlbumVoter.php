<?php

namespace Jaccob\MediaBundle\Security\Authorization\Voter;

use Jaccob\AccountBundle\Security\User\JaccobUser;
use Jaccob\MediaBundle\Model\Album;
use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class AlbumVoter extends AbstractVoter
{
    use MediaModelAware;

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
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return [self::VIEW, self::EDIT];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return [
            'Jaccob\MediaBundle\Model\Album',
            'Jaccob\MediaBundle\Model\Media',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        $isAnonymous = is_string($user);

        if (!$isAnonymous && !$user instanceof JaccobUser) {
            return false;
        }

        if ($object instanceof Album) {
            $album = $object;
        } else if ($album instanceof Media) {
            $album = $this->findAlbumOr404($album->id_album);
        }

        switch ($attribute) {

            case self::VIEW:
                // Either the user is owner, or user
                if (!$isAnonymous && $user->getAccount()->getId() === $album->id_account) {
                    return true;
                }
                // Check in session
                return $this
                    ->getAlbumModel()
                    ->isAlbumInSession(
                        $album->id,
                        // Oups
                        $this->get('session')->getId()
                    );

            case self::EDIT:
            case self::SHARE:
                // Only owner can edit his own album for now
                return !$isAnonymous && $user->getAccount()->getId() === $album->id_account;
        }

        return false;
    }
}
