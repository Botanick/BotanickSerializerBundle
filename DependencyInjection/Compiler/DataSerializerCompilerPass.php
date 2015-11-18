<?php

namespace Botanick\SerializerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataSerializerCompilerPass implements CompilerPassInterface
{
    const SERIALIZER_SERVICE_NAME = 'botanick.serializer.service';
    const TAG_NAME = 'botanick.serializer.data_serializer';

    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition(static::SERIALIZER_SERVICE_NAME)) {
            return;
        }

        $definition = $container->getDefinition(static::SERIALIZER_SERVICE_NAME);

        foreach ($container->findTaggedServiceIds(static::TAG_NAME) as $id => $attributes) {
            $definition->addMethodCall(
                'addDataSerializer',
                array(
                    new Reference($id),
                    isset($attributes['priority']) ? $attributes['priority'] : 0
                )
            );
        }
    }
}