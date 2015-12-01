<?php

namespace Jaccob\AccountBundle;

use Jaccob\AccountBundle\Security\User\JaccobUser;

/**
 * Provide a few helper functions regarding the user
 *
 * This should be used on a container aware object
 */
trait SecurityAware
{
    /**
     * Is current user logged in
     *
     * @return boolean
     */
    protected function isCurrentUserLoggedIn()
    {
        return $this
            ->container
            ->get('security.token_storage')
            ->getToken()
            ->isAuthenticated()
        ;
    }

    /**
     * Is current user anonymous
     *
     * @return boolean
     */
    protected function isCurrentUserAnonymous()
    {
        return !$this->isCurrentUserLoggedIn();
    }

    /**
     * Get current logged in security user
     *
     * @return \Jaccob\AccountBundle\Security\User\JaccobUser
     *   Note that this bundle is not dependent upon the AccountBundle so it
     *   might also be any other instance of of the 
     *   \Symfony\Component\Security\Core\User\AdvancedUserInterface
     *   interface
     *   If there is no logged in user, this returns null with no other kind
     *   of warning or error, please us isLoggedIn() or isAnonymous() before
     */
    protected function getCurrentUser()
    {
        /* @var $currentUser \Jaccob\AccountBundle\Security\User\JaccobUser */
        $token = $this->container->get('security.token_storage')->getToken();
        if ($token && !$token instanceof AnonymousToken) {
            return $token->getUser();
        }
    }

    /**
     * Get current logged in security user
     *
     * @return \Jaccob\AccountBundle\Model\Account
     *   If there is no logged in user, this returns null with no other kind
     *   of warning or error, please us isLoggedIn() or isAnonymous() before
     */
    protected function getCurrentUserAccount()
    {
        $user = $this->getCurrentUser();

        if ($user instanceof JaccobUser) {
            return $user->getAccount();
        }
    }
}
