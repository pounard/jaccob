<?php

namespace Jaccob\AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MenuProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('app.menu_builder')) {
            return;
        }

        $definition = $container->getDefinition(
            'app.menu_builder'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'app.menu_provider'
        );

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addProvider',
                array(new Reference($id))
            );
        }
    }
}
