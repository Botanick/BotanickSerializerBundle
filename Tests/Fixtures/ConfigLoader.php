<?php

namespace Botanick\SerializerBundle\Tests\Fixtures;

use Botanick\Serializer\Serializer\Config\SerializerConfigLoaderInterface;

class ConfigLoader implements SerializerConfigLoaderInterface
{
    public function getConfigFor($name)
    {
        if ($name === 'Botanick\\SerializerBundle\\Tests\\Fixtures\\SimpleClass') {
            return array(
                'default' => array(
                    'a' => null,
                    'b' => null,
                    'null' => null,
                    'c' => null,
                    'd' => array(
                        '$getter$' => 'propD'
                    ),
                    'nonexistent' => array(
                        '$default$' => 'User',
                        'format' => 'Hello, %s!'
                    )
                )
            );
        }

        return false;
    }
}