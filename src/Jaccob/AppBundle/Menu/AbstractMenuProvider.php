<?php

namespace Jaccob\AppBundle\Menu;

use Jaccob\AccountBundle\SecurityAware;

use Symfony\Component\DependencyInjection\ContainerAware;

abstract class AbstractMenuProvider extends ContainerAware implements MenuProviderInterface
{
    use SecurityAware;
}
