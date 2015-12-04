<?php

namespace Botanick\SerializerBundle\Tests;

use Botanick\SerializerBundle\BotanickSerializerBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BotanickSerializerBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $bundle = new BotanickSerializerBundle();

        $containerBuilder = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerBuilder');
        $that = $this;
        $containerBuilder
            ->expects($this->once())
            ->method('addCompilerPass')
            ->willReturnCallback(function (CompilerPassInterface $pass) use ($that) {
                $that->assertInstanceOf('Botanick\\SerializerBundle\\DependencyInjection\\Compiler\\DataSerializerCompilerPass', $pass);
            });
        /** @var ContainerBuilder $containerBuilder */

        $bundle->build($containerBuilder);
    }
}
