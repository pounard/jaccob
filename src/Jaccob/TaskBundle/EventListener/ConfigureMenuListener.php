<?php

namespace Jaccob\TaskBundle\EventListener;

use Jaccob\AppBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener
{
    /**
     * @param \Jaccob\AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('Tasks', ['route' => 'jaccob_task_list']);
    }
}