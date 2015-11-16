<?php

namespace Jaccob\MediaBundle;

use Jaccob\MediaBundle\DependencyInjection\Compiler\JobFactoryCompilerPass;
use Jaccob\MediaBundle\DependencyInjection\Compiler\TypeFinderCompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JaccobMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new JobFactoryCompilerPass());
        $container->addCompilerPass(new TypeFinderCompilerPass());
    }
}
