<?php

namespace Jaccob\AccountBundle\Menu;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\SecurityAware;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractSecurityAwareMenuProvider implements MenuProviderInterface
{
    use AccountModelAware;
    use ContainerAwareTrait;
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
