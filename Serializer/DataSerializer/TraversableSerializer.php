<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

class TraversableSerializer extends DataSerializer
{
    public function serialize($data, $group = self::GROUP_DEFAULT, $options = null)
    {
        $result = [];

        foreach ($data as $k => $v) {
            $result[$k] = $this->getSerializer()->serialize($v, $group);
        }

        return $result;
    }

    public function supports($data)
    {
        return $data instanceof \Traversable;
    }
}