<?php

namespace Jaccob\AppBundle\Menu;

use Jaccob\AccountBundle\Security\User\JaccobUser;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * Provide a few helper functions regarding the user
 *
 * Classes implement this must implement the
 * \Symfony\Component\DependencyInjectionContainerAwareInterface interface
 */
abstract class AbstractMenu extends ContainerAware
{
    /**
     * Is current user logged in
     *
     * @return boolean
     */
    protected function isLoggedIn()
    {
        return null === $this->getCurrentUser();
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
        $token = $this->container->get('security.context')->getToken();
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
    protected function getCurrentAccount()
    {
        $user = $this->getCurrentUser();

        if ($user instanceof JaccobUser) {
            return $user->getAccount();
        }
    }
}
