<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

class ResourceSerializer extends DataSerializer
{
    public function serialize($data, $group = self::GROUP_DEFAULT, $options = null)
    {
        return (string)$data;
    }

    public function supports($data)
    {
        return is_resource($data);
    }
}