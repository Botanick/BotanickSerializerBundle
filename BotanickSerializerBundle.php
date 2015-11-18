<?php

namespace Botanick\SerializerBundle;

use Botanick\SerializerBundle\DependencyInjection\Compiler\DataSerializerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BotanickSerializerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DataSerializerCompilerPass());
    }
}