<?php

namespace Botanick\SerializerBundle\Tests\DependencyInjection;

use Botanick\Serializer\SerializerInterface;
use Botanick\SerializerBundle\BotanickSerializerBundle;
use Botanick\SerializerBundle\DependencyInjection\BotanickSerializerExtension;
use Botanick\SerializerBundle\Tests\Fixtures\SimpleClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;

class BotanickSerializerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    protected static $_filesystem;
    protected static $_cacheDir;

    public static function setUpBeforeClass()
    {
        self::$_filesystem = new Filesystem();
        self::$_cacheDir = sys_get_temp_dir() . '/' . uniqid('botanick-serializer-');
    }

    protected function setUp()
    {
        self::$_filesystem->remove(self::$_cacheDir);
    }

    protected function tearDown()
    {
        self::$_filesystem->remove(self::$_cacheDir);
    }

    public function testLoad()
    {
        $container = $this->getContainerForConfig('default');

        $this->assertTrue($container->has('botanick.serializer.service'));
        $this->assertTrue($container->has('botanick.serializer'));
        $this->assertSame($container->get('botanick.serializer.service'), $container->get('botanick.serializer'));
    }

    /**
     * @param string $configName
     * @param string $expectedConfigLoader
     * @dataProvider configLoaderLoadProvider
     */
    public function testConfigLoaderLoad($configName, $expectedConfigLoader)
    {
        $container = $this->getContainerForConfig($configName);

        $this->assertTrue($container->has($expectedConfigLoader));
        $this->assertTrue($container->has('botanick.serializer.config_loader'));
        $this->assertSame($container->get($expectedConfigLoader), $container->get('botanick.serializer.config_loader'));
    }

    /**
     * @param string $configName
     * @param mixed $expected
     * @param mixed $data
     * @dataProvider serializingProvider
     */
    public function testSerializing($configName, $expected, $data)
    {
        $container = $this->getContainerForConfig($configName);

        /** @var SerializerInterface $serializer */
        $serializer = $container->get('botanick.serializer');

        $this->assertSame($expected, $serializer->serialize($data));
    }

    public function configLoaderLoadProvider()
    {
        return array(
            array('default', 'botanick.serializer.config_loader.bundles'),
            array('array', 'botanick.serializer.config_loader.array'),
            array('files', 'botanick.serializer.config_loader.files'),
            array('dirs', 'botanick.serializer.config_loader.dirs'),
            array('bundles', 'botanick.serializer.config_loader.bundles'),
            array('service', 'my_config_loader')
        );
    }

    public function serializingProvider()
    {
        return array(
            array('array', null, null),
            array('array', true, true),
            array('array', 1, 1),
            array('array', 'smth', 'smth'),
            array('array', 1448560065, \DateTime::createFromFormat('H:i:s d.m.Y', '17:47:45 26.11.2015', new \DateTimeZone('UTC'))),
            array('array', array('a' => 1, 'b' => 'b', 'c' => null), array('a' => 1, 'b' => 'b', 'c' => null)),
            array('service', array('a' => 1, 'b' => 'b', 'null' => null, 'c' => 'c', 'd' => 'd', 'nonexistent' => 'Hello, User!'), new SimpleClass())
        );
    }

    /**
     * @param string $configFilename
     * @return ContainerBuilder
     */
    private function getContainerForConfig($configFilename)
    {
        $container = new ContainerBuilder(
            new ParameterBag(
                array(
                    'kernel.debug' => true,
                    'kernel.cache_dir' => self::$_cacheDir,
                    'kernel.name' => 'App',
                    'kernel.environment' => 'test',
                    'kernel.bundles' => array(
                        'BotanickSerializerBundle' => 'Botanick\\SerializerBundle\\BotanickSerializerBundle'
                    )
                )
            )
        );

        $fileLocator = $this->getMockBuilder('Symfony\\Component\\HttpKernel\\Config\\FileLocator')
            ->disableOriginalConstructor()
            ->getMock();
        $container->set('file_locator', $fileLocator);

        $container->registerExtension(new BotanickSerializerExtension());

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Fixtures/config'));
        $loader->load($configFilename . '.yml');

        $bundle = new BotanickSerializerBundle();
        $bundle->build($container);

        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
