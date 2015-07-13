<?php

namespace Jaccob\AppBundle;

use Jaccob\AppBundle\DependencyInjection\MenuProviderCompilerPass;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JaccobAppBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MenuProviderCompilerPass());
  }
}
