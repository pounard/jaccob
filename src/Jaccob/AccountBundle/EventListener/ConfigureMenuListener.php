<?php

namespace Jaccob\AccountBundle\EventListener;

use Jaccob\AppBundle\Event\ConfigureMenuEvent;

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

            $accountMenu = $menu->addChild($account->getUsername(), [
                'route' => 'jaccob_account.profile_view',
                'routeParameters' => array('id' => $account->getId()),
            ]);
            $accountMenu->addChild('My account', [
                'route' => 'jaccob_account.profile_view',
                'routeParameters' => array('id' => $account->getId()),
            ]);
            $accountMenu->addChild('Logout', ['route' => 'jaccob_account.logout']);

        } else {
            $menu->addChild('Log-in',  ['route' => 'jaccob_account.login']);
        }
    }
}