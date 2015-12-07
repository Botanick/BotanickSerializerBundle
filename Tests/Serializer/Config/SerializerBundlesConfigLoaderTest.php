<?php

namespace Botanick\SerializerBundle\Tests\Serializer\Config;

use Botanick\Serializer\Exception\ConfigLoadException;
use Botanick\Serializer\Serializer\Config\SerializerConfigCache;
use Botanick\SerializerBundle\Serializer\Config\SerializerBundlesConfigLoader;
use Symfony\Component\Config\FileLocatorInterface;

class SerializerBundlesConfigLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $bundles
     * @param mixed $expectedConfig
     * @param string $name
     * @dataProvider getConfigForWithoutCacheProvider
     */
    public function testGetConfigForWithoutCache($bundles, $expectedConfig, $name)
    {
        $configLoader = $this->getConfigLoader($bundles, null, sizeof($bundles));

        $this->assertSame($expectedConfig, $configLoader->getConfigFor($name));
    }

    /**
     * @param array $bundles
     * @param int $locatorCalls
     * @throws \Exception
     * @dataProvider getConfigForWithoutCacheExceptionProvider
     */
    public function testGetConfigForWithoutCacheException($bundles, $locatorCalls)
    {
        $configLoader = $this->getConfigLoader($bundles, null, $locatorCalls);

        $this->setExpectedException(
            'Botanick\\Serializer\\Exception\\ConfigLoadException',
            'Unable to find "botanick-serializer" directory in @BlahBundle bundle.'
        );

        try {
            $configLoader->getConfigFor('test');
        } catch (ConfigLoadException $ex) {
            $this->assertInstanceOf('\InvalidArgumentException', $ex->getPrevious());

            throw $ex;
        }
    }

    public function testGetConfigForWithCache()
    {
        $config = array('test' => array('a' => 1));

        $configCache = $this->getMockBuilder('Botanick\\Serializer\\Serializer\\Config\\SerializerConfigCache')
            ->disableOriginalConstructor()
            ->getMock();

        $configCache
            ->expects($this->once())
            ->method('getCachedConfig')
            ->willReturn($config);
        /** @var SerializerConfigCache $configCache */

        $configLoader = $this->getConfigLoader(
            array('@FooBundle', '@BarBundle'),
            $configCache
        );

        $this->assertSame($config['test'], $configLoader->getConfigFor('test'));
    }

    public function testGetConfigForWithCacheMiss()
    {
        $configCache = $this->getMockBuilder('Botanick\\Serializer\\Serializer\\Config\\SerializerConfigCache')
            ->disableOriginalConstructor()
            ->getMock();

        $that = $this;
        $configCache
            ->expects($this->once())
            ->method('getCachedConfig')
            ->willReturnCallback(
                function ($type, $sources, $createConfigCallback) use ($that) {
                    $that->assertInternalType('string', $type);
                    $that->assertNotEmpty($type);
                    $that->assertInternalType('array', $sources);
                    $that->assertEquals(2, sizeof($sources));

                    list($config, $filesAndDirs) = call_user_func($createConfigCallback);
                    $that->assertInternalType('array', $filesAndDirs);
                    $that->assertEquals(2, sizeof($filesAndDirs));

                    return $config;
                }
            );
        /** @var SerializerConfigCache $configCache */

        $configLoader = $this->getConfigLoader(
            array('@FooBundle', '@BarBundle'),
            $configCache,
            2
        );

        $this->assertSame(array('foo' => 1), $configLoader->getConfigFor('FooEntity'));
        $this->assertSame(array('bar' => 1), $configLoader->getConfigFor('BarEntity'));
        $this->assertSame(array('x' => 'bar'), $configLoader->getConfigFor('AnotherEntity'));
    }

    public function getConfigForWithoutCacheProvider()
    {
        return array(
            // single bundle
            array(array('@FooBundle'), array('foo' => 1), 'FooEntity'),
            array(array('@BarBundle'), array('bar' => 1), 'BarEntity'),
            // multiple bundles
            array(array('@FooBundle', '@BarBundle'), array('foo' => 1), 'FooEntity'),
            array(array('@FooBundle', '@BarBundle'), array('bar' => 1), 'BarEntity'),
            // config overloading
            array(array('@FooBundle', '@BarBundle'), array('x' => 'bar'), 'AnotherEntity'),
            array(array('@BarBundle', '@FooBundle'), array('x' => 'foo'), 'AnotherEntity')
        );
    }

    public function getConfigForWithoutCacheExceptionProvider()
    {
        // order matters!
        return array(
            array(array('@FooBundle', '@BarBundle', '@BlahBundle'), 3),
            array(array('@FooBundle', '@BlahBundle', '@BarBundle'), 2),
            array(array('@BlahBundle', '@FooBundle', '@BarBundle'), 1)
        );
    }

    /**
     * @param array $bundles
     * @param SerializerConfigCache $cache
     * @param int $locatorCalls
     * @return SerializerBundlesConfigLoader
     */
    protected function getConfigLoader(array $bundles = array(), SerializerConfigCache $cache = null, $locatorCalls = 0)
    {
        $fileLocator = $this->getMock('Symfony\\Component\\Config\\FileLocatorInterface');
        $that = $this;
        $fileLocator
            ->expects($this->exactly($locatorCalls))
            ->method('locate')
            ->willReturnCallback(
                function ($name, $currentPath, $first) use ($that, $bundles) {
                    $that->assertContains('@', $name);
                    $that->assertContains('/Resources/config/botanick-serializer', $name);
                    $that->assertNull($currentPath);
                    $that->assertFalse($first);

                    if (preg_match('~(FooBundle|BarBundle)~', $name)) {
                        return array(str_replace('@', __DIR__ . '/../../Fixtures/', $name));
                    } else {
                        throw new \InvalidArgumentException();
                    }
                }
            );
        /** @var FileLocatorInterface $fileLocator */

        $configLoader = new SerializerBundlesConfigLoader($fileLocator, $bundles, $cache);

        return $configLoader;
    }
}
