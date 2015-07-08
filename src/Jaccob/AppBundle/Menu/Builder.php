<?php

namespace Jaccob\AppBundle\Menu;

use Knp\Menu\FactoryInterface;

class Builder extends AbstractMenu
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $account = $this->getAccount();

        if ($account) {
            $menu->addChild('Tasks', ['route' => 'jaccob_task_list']);
            $menu->addChild($account->getUsername(), ['route' => 'jaccob_account_login']);
            $menu->addChild('Logout', ['route' => 'jaccob_account_logout']);
        }

        return $menu;
    }
}
