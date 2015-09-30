<?php

namespace Jaccob\AccountBundle\Security\User;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\Security;
use Jaccob\AccountBundle\Security\Access;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class JaccobAccountProvider implements UserProviderInterface
{
    use AccountModelAware;

    /**
     * {inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $account = $this
            ->getAccountModel()
            ->findUserByMail($username)
        ;

        if (!$account) {
            throw new UsernameNotFoundException();
        }

        // @todo Better than this
        if ($account->get('is_admin')) {
            $roles = [Access::ROLE_NORMAL, Access::ROLE_ADMIN];
        } else {
            $roles = [Access::ROLE_NORMAL];
        }

        return new JaccobUser($account, $roles);
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
