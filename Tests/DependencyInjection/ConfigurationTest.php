<?php

namespace Botanick\SerializerBundle\Tests\DependencyInjection;

use Botanick\SerializerBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $expectedConfig
     * @param string $bundleConfig
     * @dataProvider testConfigurationProvider
     */
    public function testConfiguration($expectedConfig, $bundleConfig)
    {
        $this->assertEquals($expectedConfig, $this->processConfiguration($bundleConfig));
    }

    public function testConfigurationProvider()
    {
        $defaultConfig = array(
            'config_loader' => array(
                'type' => 'bundles',
                'array' => array(),
                'files' => array(),
                'dirs' => array(),
                'bundles' => array(),
                'service' => null
            ),
            'data_serializers' => array(
                'scalar' => array('priority' => -9999, 'options' => array('type' => false, 'format' => false)),
                'resource' => array('priority' => -9999, 'options' => array()),
                'null' => array('priority' => -9999, 'options' => array()),
                'array' => array('priority' => -9999, 'options' => array()),
                'traversable' => array('priority' => -8888, 'options' => array()),
                'datetime' => array('priority' => -8888, 'options' => array('format' => false)),
                'object' => array('priority' => -9999, 'options' => array())
            )
        );
        $modifiedConfig = $defaultConfig;
        $modifiedConfig['data_serializers']['scalar']['options']['type'] = 'int';
        $mergedConfig = $defaultConfig;
        $mergedConfig['data_serializers']['scalar']['options']['param'] = 'value';

        return array(
            // empty config
            array($defaultConfig, array()),
            // some rows of default config
            array($defaultConfig, array('config_loader' => array('type' => 'bundles'), 'data_serializers' => array('null' => array('priority' => -9999), 'datetime' => array('options' => array('format' => false))))),
            // serializers' modification
            array($modifiedConfig, array('data_serializers' => array('scalar' => array('options' => array('type' => 'int'))))),
            // serializers' options merge
            array($mergedConfig, array('data_serializers' => array('scalar' => array('options' => array('param' => 'value')))))
        );
    }

    protected function processConfiguration($config)
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), array($config));
    }
}
