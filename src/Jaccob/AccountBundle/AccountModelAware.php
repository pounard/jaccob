<?php

namespace Jaccob\AccountBundle;

use PommProject\Foundation\Session\Session;

trait AccountModelAware
{
    /**
     * @var \PommProject\Foundation\Session\Session
     */
    private $pommSession;

    /**
     * Set session
     *
     * @param \PommProject\Foundation\Session\Session $pommSession
     */
    protected function setAccountSession(Session $pommSession)
    {
        $this->pommSession = $pommSession;
    }

    /**
     * Get pomm account session
     *
     * @return \PommProject\Foundation\Session\Session
     */
    protected function getAccountSession()
    {
        if ($this->pommSession) {
            return $this->pommSession;
        }

        // When we are working with an object plugged to the DIC.
        return $this->get('pomm')->getSession('account');
    }

    /**
     * Get pomm account model
     *
     * @return \Jaccob\AccountBundle\Model\Account\PublicSchema\AccountModel
     */
    protected function getAccountModel()
    {
        return $this
            ->getAccountSession()
            ->getModel('\Jaccob\AccountBundle\Model\Account\PublicSchema\AccountModel');
    }
}