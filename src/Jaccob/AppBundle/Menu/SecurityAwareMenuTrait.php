<?php

namespace Jaccob\AppBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * Provide a few helper functions regarding the user
 *
 * Classes implement this must implement the
 * \Symfony\Component\DependencyInjectionContainerAwareInterface interface
 */
trait SecurityAwareMenuTrait
{
    /**
     * Is current user logged in
     *
     * @return boolean
     */
    protected function isLoggedIn()
    {
        return null === $this->getAccount();
    }

    /**
     * Is current user anonymous
     *
     * @return boolean
     */
    protected function isAnonymous()
    {
        return !$this->isLoggedIn();
    }

    /**
     * Get current logged in user account
     *
     * @return \Jaccob\AccountBundle\Security\User\JaccobUser
     *   Note that this bundle is not dependent upon the AccountBundle so it
     *   might also be any other instance of of the 
     *   \Symfony\Component\Security\Core\User\AdvancedUserInterface
     *   interface
     *   If there is no logged in user, this returns null with no other kind
     *   of warning or error, please us isLoggedIn() or isAnonymous() before
     */
    protected function getAccount()
    {
        /* @var $currentUser \Jaccob\AccountBundle\Security\User\JaccobUser */
        $token = $this->container->get('security.context')->getToken();
        if ($token && !$token instanceof AnonymousToken) {
            return $token->getUser();
        }
    }
}
