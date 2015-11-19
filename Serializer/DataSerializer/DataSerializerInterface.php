<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

use Botanick\SerializerBundle\SerializerInterface;

interface DataSerializerInterface extends SerializerInterface
{
    /**
     * @param $data
     * @return bool
     */
    public function supports($data);
}