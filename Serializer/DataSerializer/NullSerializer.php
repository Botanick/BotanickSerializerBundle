<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

class NullSerializer extends DataSerializer
{
    public function serialize($data, $group = self::GROUP_DEFAULT, $options = null)
    {
        return $data;
    }

    public function supports($data)
    {
        return is_null($data);
    }
}