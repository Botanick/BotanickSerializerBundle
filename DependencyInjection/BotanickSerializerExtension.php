<?php

namespace Botanick\SerializerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class BotanickSerializerExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $container->setParameter('botanick.serializer.config.bundles', $mergedConfig['bundles']);

        foreach ($mergedConfig['data_serializers'] as $name => $options) {
            $container->setParameter(sprintf('botanick.serializer.config.data_serializer.%s.priority', $name), $options['priority']);
            $container->setParameter(sprintf('botanick.serializer.config.data_serializer.%s.options', $name), $options['options']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}