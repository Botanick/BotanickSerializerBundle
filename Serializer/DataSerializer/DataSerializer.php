<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

use Botanick\SerializerBundle\SerializerAwareInterface;
use Botanick\SerializerBundle\SerializerInterface;

abstract class DataSerializer implements DataSerializerInterface, SerializerAwareInterface
{
    /**
     * @var SerializerInterface
     */
    private $_serializer;

    public function __construct()
    {

    }

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->_serializer = $serializer;
    }

    protected function getSerializer()
    {
        return $this->_serializer;
    }
}