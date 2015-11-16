<?php

namespace Jaccob\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TypeFinderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jaccob_media.type_finder')) {
            return;
        }

        $definition = $container->getDefinition('jaccob_media.type_finder');

        $taggedServices = $container->findTaggedServiceIds('jaccob_media.type');

        foreach ($taggedServices as $id => $attributes) {

            // FIXME I am not proud of this one but I can't manage to set
            // arbitrary attributes on my tagged services
            $typeDefinition = $container->getDefinition($id);
            $mimetypes = $typeDefinition->getArguments();
            $typeDefinition->setArguments([]);

            $definition->addMethodCall(
                'addType',
                [$mimetypes, new Reference($id)]
            );
        }
    }
}
