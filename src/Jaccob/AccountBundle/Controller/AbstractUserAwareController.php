<?php

namespace Jaccob\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AbstractUserAwareController extends Controller
{
    /**
     * Get current logged in security user
     *
     * @return \Jaccob\AccountBundle\Security\User\JaccobUser
     */
    public function getCurrentUser()
    {
        return $this
            ->get('security.context')
            ->getToken()
            ->getUser()
        ;
    }

    /**
     * Is the current user anonymous
     *
     * @return boolean
     */
    public function isCurrentUserAnonymous()
    {
        return $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_ANONYMOUSLY');
    }

    /**
     * Get current logged in user account
     *
     * @return \Jaccob\AccountBundle\Model\Account
     */
    public function getCurrentUserAccount()
    {
        return $this
            ->getCurrentUser()
            ->getAccount()
        ;
    }
}
