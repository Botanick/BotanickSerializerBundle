<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

use Botanick\SerializerBundle\SerializerInterface;

interface DataSerializerInterface extends SerializerInterface
{
    /**
     * @param mixed $data
     * @param string $group
     * @param mixed $config
     * @return mixed
     */
    public function serialize($data, $group = self::GROUP_DEFAULT, $config = null);

    /**
     * @param $data
     * @return bool
     */
    public function supports($data);
}