<?php

namespace Jaccob\AccountBundle\Menu;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\SecurityAware;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;

use Symfony\Component\DependencyInjection\ContainerAware;

abstract class AbstractSecurityAwareMenuProvider extends ContainerAware implements MenuProviderInterface
{
    use AccountModelAware;
    use SecurityAware;

    /**
     * @var \Knp\Menu\FactoryInterface
     */
    protected $factory;

    /**
     * Default constructor
     *
     * @param \Knp\Menu\FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }
}
