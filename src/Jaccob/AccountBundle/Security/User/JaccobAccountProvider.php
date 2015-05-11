<?php

namespace Jaccob\AccountBundle\Security\User;

use Jaccob\AccountBundle\Security;

use PommProject\Foundation\Session\Session as PommSession;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class JaccobAccountProvider implements UserProviderInterface
{
    /**
     * @var \PommProject\Foundation\Session\Session
     */
    protected $pommSession;

    /**
     * Default constructor
     *
     * @param \PommProject\Foundation\Session\Session $pommSession
     */
    public function __construct(PommSession $pommSession)
    {
        $this->pommSession = $pommSession;
    }

    /**
     * {inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        /* @var $account \Jaccob\AccountBundle\Model\Account\PublicSchema\Account */
        $account = $this
            ->pommSession
            ->getModel('\Jaccob\AccountBundle\Model\Account\PublicSchema\AccountModel')
            ->findUserByMail($username)
        ;

        if (!$account) {
            throw new UsernameNotFoundException();
        }

        return new JaccobUser($account->getUsername(), $account->getPasswordHash(), $account->getSalt());
    }

    /**
     * {inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException();
        }

        // @todo Nothing else than username and password matters for now.
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Jaccob\AccountBundle\Security\User\JaccobUser';
    }
}
