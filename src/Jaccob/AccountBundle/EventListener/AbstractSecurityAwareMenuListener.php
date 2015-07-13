<?php

namespace Jaccob\AccountBundle\EventListener;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\SecurityAware;

use Symfony\Component\DependencyInjection\ContainerAware;

abstract class AbstractSecurityAwareMenuListener extends ContainerAware
{
    use AccountModelAware;
    use SecurityAware;
}
