<?php

namespace Jaccob\AppBundle\Menu;

use Jaccob\AccountBundle\SecurityAware;

use Knp\Menu\FactoryInterface;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilder extends ContainerAware
{
    use SecurityAware;

    /**
     * @var \Knp\Menu\FactoryInterface
     */
    private $factory;

    /**
     * @var \Jaccob\AppBundle\Menu\MenuProviderInterface[]
     */
    private $providers = [];

    /**
     * Default constructor
     *
     * @param \Knp\Menu\FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Add a menu provider
     *
     * @param \Jaccob\AppBundle\Menu\MenuProviderInterface $provider
     */
    public function addProvider(MenuProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * Create main menu
     *
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function createMainMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');

        $account = $this->getCurrentAccount();

        if ($account) {

            $menu->addChild($account->getUsername(), [
                'route' => 'jaccob_account_profile_view',
                'routeParameters' => array('id' => $account->getId()),
            ]);
            $menu->addChild('Logout', ['route' => 'jaccob_account_logout']);
        }

        foreach ($this->providers as $provider) {
            $provider->attachMainMenuChildren($menu, $requestStack);
        }

        return $menu;
    }
}
