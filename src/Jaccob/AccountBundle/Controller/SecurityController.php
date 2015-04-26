<?php

namespace Jaccob\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Intl\Exception\NotImplementedException;

class SecurityController extends Controller
{
    /**
     * Get account database connector
     *
     * @return \PommProject\Foundation\Session\Session
     */
    public function getAccountDatabase()
    {
        if (!$this->container->has('pomm')) {
            throw new \LogicException("POMM service is not installed");
        }

        return $this->container->get('pomm')['account'];
    }

    public function loginAction()
    {
        throw new NotImplementedException("Not implemented");

        // FOUQUE!!! getModel() ???
        $this
            ->getAccountDatabase()
        ;
    }
}
