<?php

namespace Botanick\SerializerBundle\Serializer\Config;

use Botanick\SerializerBundle\Exception\ConfigNotFoundException;

interface SerializerConfigLoaderInterface
{
    /**
     * @param string $name
     * @return mixed
     * @throws ConfigNotFoundException
     */
    public function getConfigFor($name);
}