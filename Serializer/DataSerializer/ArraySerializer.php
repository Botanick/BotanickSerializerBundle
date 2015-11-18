<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

class ArraySerializer extends DataSerializer
{
    public function serialize($data, $group = self::GROUP_DEFAULT, $config = null)
    {
        $result = [];

        foreach ($data as $k => $v) {
            $result[$k] = $this->getSerializer()->serialize($v, $group);
        }

        return $result;
    }

    public function supports($data)
    {
        return is_array($data);
    }
}