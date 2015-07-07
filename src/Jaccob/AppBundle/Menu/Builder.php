<?php

namespace Jaccob\AppBundle\Menu;

use Knp\Menu\FactoryInterface;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', ['route' => '_welcome']);

        /* @var $currentUser \Jaccob\AccountBundle\Security\User\JaccobUser */
        $token = $this->container->get('security.context')->getToken();
        if ($token && !$token instanceof AnonymousToken) {
            $currentUser = $token->getUser();
            if ($currentUser) {

                $menu->addChild('Tasks', ['route' => 'jaccob_task_list']);

                $menu->addChild($currentUser->getUsername(), ['route' => 'jaccob_account_login']);
                $menu->addChild('Logout', ['route' => 'jaccob_account_logout']);
            }
        }

        return $menu;
    }
}
