<?php

namespace Jaccob\MediaBundle\EventListener;

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

        $account = $this->getCurrentUserAccount();

        if ($account) {
            $menu->addChild('Medias', ['route' => 'jaccob_media.home']);
        }
    }
}