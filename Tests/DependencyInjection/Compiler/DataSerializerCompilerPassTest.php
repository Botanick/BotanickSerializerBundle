<?php

namespace Botanick\SerializerBundle\Tests\DependencyInjection\Compiler;

use Botanick\SerializerBundle\DependencyInjection\Compiler\DataSerializerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataSerializerCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithoutService()
    {
        $pass = new DataSerializerCompilerPass();

        $containerBuilder = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('hasDefinition', 'getDefinition', 'findTaggedServiceIds'))
            ->getMock();
        $containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('botanick.serializer.service')
            ->willReturn(false);
        $containerBuilder
            ->expects($this->never())
            ->method('getDefinition');
        $containerBuilder
            ->expects($this->never())
            ->method('findTaggedServiceIds');
        /** @var ContainerBuilder $containerBuilder */

        $pass->process($containerBuilder);
    }

    public function testProcessWithoutTaggedServices()
    {
        $pass = new DataSerializerCompilerPass();

        $service = $this->getMock('Symfony\\Component\\DependencyInjection\\Definition');
        $service
            ->expects($this->never())
            ->method('addMethodCall');

        $containerBuilder = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('hasDefinition', 'getDefinition', 'findTaggedServiceIds'))
            ->getMock();
        $containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('botanick.serializer.service')
            ->willReturn(true);
        $containerBuilder
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($service);
        $containerBuilder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn(array());
        /** @var ContainerBuilder $containerBuilder */

        $pass->process($containerBuilder);
    }

    public function testProcessWithTaggedServices()
    {
        $pass = new DataSerializerCompilerPass();

        $taggedServices = array(
            'service1' => array(array('priority' => -10)),
            'service2' => array(array('priority' => 0)),
            'service3' => array(array()),
            'service4' => array(array('priority' => 10))
        );

        $that = $this;
        $service = $this->getMock('Symfony\\Component\\DependencyInjection\\Definition');
        $subscribedServicesPointer = 0;
        $subscribedServices = array(
            array('service1', -10),
            array('service2', 0),
            array('service3', 0),
            array('service4', 10)
        );
        $service
            ->expects($this->exactly(4))
            ->method('addMethodCall')
            ->willReturnCallback(
                function ($method, array $arguments) use ($that, $subscribedServices, &$subscribedServicesPointer) {
                    $that->assertSame('addDataSerializer', $method);
                    $that->assertCount(2, $arguments);
                    $that->assertInstanceOf('Symfony\\Component\\DependencyInjection\\Reference', $arguments[0]);
                    /** @var Reference $reference */
                    $reference = $arguments[0];
                    $that->assertSame($subscribedServices[$subscribedServicesPointer][0], (string)$reference);
                    // priority
                    $that->assertSame($subscribedServices[$subscribedServicesPointer][1], $arguments[1]);

                    $subscribedServicesPointer++;
                }
            );

        $containerBuilder = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('hasDefinition', 'getDefinition', 'findTaggedServiceIds'))
            ->getMock();
        $containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('botanick.serializer.service')
            ->willReturn(true);
        $containerBuilder
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($service);
        $containerBuilder
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn($taggedServices);
        /** @var ContainerBuilder $containerBuilder */

        $pass->process($containerBuilder);
    }
}
