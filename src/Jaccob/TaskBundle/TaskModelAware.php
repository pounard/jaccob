<?php

namespace Jaccob\TaskBundle;

use PommProject\Foundation\Session;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

trait TaskModelAware
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
    public function setPommSession(Session $pommSession)
    {
        $this->pommSession = $pommSession;
    }

    /**
     * Get pomm session
     *
     * @return \PommProject\Foundation\Session
     */
    protected function getPommSession()
    {
        if ($this->pommSession) {
            return $this->pommSession;
        }

        if ($this instanceof ContainerAwareInterface) {
            return $this->container->get('pomm')->getSession('default');
        }

        if (method_exists($this, 'get')) {
            return $this->get('pomm')->getSession('default');
        }
    }

    /**
     * Get pomm task model
     *
     * @return \Jaccob\TaskBundle\Model\TaskModel
     */
    protected function getTaskModel()
    {
        return $this
            ->getPommSession()
            ->getModel('\Jaccob\TaskBundle\Model\TaskModel')
        ;
    }

    /**
     * Get pomm tag model
     *
     * @return \Jaccob\TaskBundle\Model\TagModel
     */
    protected function getTagModel()
    {
        return $this
            ->getPommSession()
            ->getModel('\Jaccob\TaskBundle\Model\TagModel')
        ;
    }
}
