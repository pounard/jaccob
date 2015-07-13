<?php

namespace Jaccob\AppBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

use Symfony\Component\EventDispatcher\Event;

class ConfigureMenuEvent extends Event
{
    const CONFIGURE = 'jaccob.menu_configure';

    /**
     * @var \Knp\Menu\FactoryInterface $factory
     */
    private $factory;

    /**
     * @var \Knp\Menu\ItemInterface $menu
     */
    private $menu;

    /**
     * Default constructor
     *
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface $menu
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    /**
     * Get menu factory
     *
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Get menu being built
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
