<?php

namespace Jaccob\TaskBundle\EventListener;

use Jaccob\AppBundle\Event\ConfigureMenuEvent;

use Jaccob\AccountBundle\EventListener\AbstractSecurityAwareMenuListener;

class ConfigureMenuListener extends AbstractSecurityAwareMenuListener
{
    /**
     * @param \Jaccob\AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $account = $this->getCurrentAccount();

        if ($account) {
            $menu->addChild('Tasks', ['route' => 'jaccob_task.list']);
        }
    }
}