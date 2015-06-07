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
