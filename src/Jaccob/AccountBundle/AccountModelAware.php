<?php

namespace Jaccob\AccountBundle;

use PommProject\Foundation\Session;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

trait AccountModelAware
{
    /**
     * @var \PommProject\Foundation\Session
     */
    private $pommSession;

    /**
     * Set session
     *
     * @param \PommProject\Foundation\Session $pommSession
     */
    public function setAccountSession(Session $pommSession)
    {
        $this->pommSession = $pommSession;
    }

    /**
     * Get pomm account session
     *
     * @return \PommProject\Foundation\Session
     */
    protected function getAccountSession()
    {
        if ($this->pommSession) {
            return $this->pommSession;
        }

        // When we are working with an object plugged to the DIC.
        if ($this instanceof ContainerAwareInterface) {
            return $this->container->get('pomm')->getSession('default');
        }

        // When we are working with a controller.
        return $this->get('pomm')->getSession('default');
    }

    /**
     * Get pomm account model
     *
     * @return \Jaccob\AccountBundle\Model\AccountModel
     */
    protected function getAccountModel()
    {
        return $this
            ->getAccountSession()
            ->getModel('\Jaccob\AccountBundle\Model\AccountModel')
        ;
    }

    /**
     * Get account or throw a 404 or 403 error depending on data
     *
     * @param int $id
     *   Account identifier
     *
     * @return \Jaccob\AccountBundle\Model\Account
     */
    protected function findAccountOr404($id)
    {
        /* @var $account \Jaccob\AccountBundle\Model\Account */
        $account = $this->getAccountModel()->findByPK(['id' => $id]);

        if (!$account) {
            throw $this->createNotFoundException(sprintf(
                "Account with id '%d' does not exists",
                $id
            ));
        }

        return $account;
    }
}
