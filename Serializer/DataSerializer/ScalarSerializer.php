<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

class ScalarSerializer extends DataSerializer
{
    public function serialize($data, $group = self::GROUP_DEFAULT, $config = null)
    {
        return $data;
    }

    public function supports($data)
    {
        return is_scalar($data);
    }
}