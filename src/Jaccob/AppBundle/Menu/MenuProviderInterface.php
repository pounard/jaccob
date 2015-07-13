<?php

namespace Jaccob\AppBundle\Menu;

use Knp\Menu\ItemInterface;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Bundles that want to add items to the menu should listen to this
 */
interface MenuProviderInterface
{
    /**
     * Attach children to the main menu
     *
     * @param \Knp\Menu\ItemInterface $root
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function attachMainMenuChildren(ItemInterface $root, RequestStack $requestStack);
}
