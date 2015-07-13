<?php

namespace Jaccob\AppBundle\Menu;

use Jaccob\AccountBundle\Menu\AbstractSecurityAwareMenuProvider;
use Jaccob\AppBundle\Event\ConfigureMenuEvent;

class MainMenuProvider extends AbstractSecurityAwareMenuProvider
{
    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = array())
    {
        switch ($name) {

            case 'main':
                $menu = $this->factory->createItem('root');

                $account = $this->getCurrentAccount();

                if ($account) {
                    $menu->addChild($account->getUsername(), [
                        'route' => 'jaccob_account_profile_view',
                        'routeParameters' => array('id' => $account->getId()),
                    ]);
                    $menu->addChild('Logout', ['route' => 'jaccob_account_logout']);
                }

                $this->container->get('event_dispatcher')->dispatch(
                    ConfigureMenuEvent::CONFIGURE,
                    new ConfigureMenuEvent($this->factory, $menu)
                );

                return $menu;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = array())
    {
        switch ($name) {

            case 'main':
                return true;

            default:
                return false;
        }
    }
}
