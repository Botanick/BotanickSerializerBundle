<?php

namespace Botanick\SerializerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    protected $_dataSerializers = [
        'scalar' => ['priority' => -9999, 'options' => ['type' => false, 'format' => false]],
        'resource' => ['priority' => -9999, 'options' => []],
        'null' => ['priority' => -9999, 'options' => []],
        'array' => ['priority' => -9999, 'options' => []],
        'traversable' => ['priority' => -8888, 'options' => []],
        'datetime' => ['priority' => -8888, 'options' => ['format' => false]],
        'object' => ['priority' => -9999, 'options' => []]
    ];

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder
            ->root('botanick_serializer', 'array')
            ->children();

        $this->addConfigLoaderSection($root);
        $this->addDataSerializersSection($root);

        return $treeBuilder;
    }

    protected function addConfigLoaderSection(NodeBuilder $builder)
    {
        $builder
            ->arrayNode('config_loader')
                ->addDefaultsIfNotSet()
                ->children()
                    ->enumNode('type')
                        ->defaultValue('bundles')
                        ->values(['array', 'files', 'bundles', 'service'])
                    ->end()
                    ->arrayNode('array')
                        ->defaultValue([])
                        ->prototype('variable')->end()
                    ->end()
                    ->arrayNode('files')
                        ->defaultValue([])
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('bundles')
                        ->defaultValue([])
                        ->prototype('scalar')->end()
                    ->end()
                    ->scalarNode('service')
                        ->defaultNull()
                    ->end()
                ->end()
                ->validate()
                    ->ifTrue(function ($config) {
                        return $config['type'] === 'service' && (!is_string($config['service']) || empty($config['service']));
                    })
                    ->thenInvalid('You must define "service" option.')
                ->end()
            ->end();
    }

    protected function addDataSerializersSection(NodeBuilder $builder)
    {
        $node = $builder
            ->arrayNode('data_serializers')
                ->addDefaultsIfNotSet()
                ->fixXmlConfig('data_serializer', 'data_serializers')
                ->children();

        foreach ($this->_dataSerializers as $name => $options) {
            $this->addDataSerializerSection($node, $name, $options);
        }
    }

    protected function addDataSerializerSection(NodeBuilder $builder, $name, array $options)
    {
        $builder
            ->arrayNode($name)
                ->addDefaultsIfNotSet()
                ->children()
                    ->integerNode('priority')->defaultValue($options['priority'])->end()
                    ->arrayNode('options')
                        ->fixXmlConfig('option', 'options')
                        ->defaultValue($options['options'])
                        ->prototype('scalar')->end()
                        ->beforeNormalization()
                            ->always(
                                function ($v) use ($options) {
                                    return array_merge($options['options'], $v);
                                }
                            )
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}