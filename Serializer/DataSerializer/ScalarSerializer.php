<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

class ScalarSerializer extends DataSerializer
{
    /**
     * @param mixed $data
     * @param string $group
     * @param mixed $options
     * @return mixed
     */
    public function serialize($data, $group = self::GROUP_DEFAULT, $options = null)
    {
        return $data;
    }

    public function supports($data)
    {
        return is_scalar($data);
    }
}