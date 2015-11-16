<?php

namespace Jaccob\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class JobFactoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jaccob_media.job_factory')) {
            return;
        }

        $definition = $container->getDefinition('jaccob_media.job_factory');

        $taggedServices = $container->findTaggedServiceIds('jaccob_media.job');

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addJob',
                [$attributes[0]['alias'], new Reference($id)]
            );
        }
    }
}
