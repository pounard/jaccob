<?php

namespace Jaccob\AccountBundle\EventListener;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\SecurityAware;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractSecurityAwareMenuListener
{
    use AccountModelAware;
    use ContainerAwareTrait;
    use SecurityAware;
}
