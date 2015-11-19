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

    /**
     * @return SerializerInterface
     */
    protected function getSerializer()
    {
        return $this->_serializer;
    }

    /**
     * @param mixed $options
     * @return array
     */
    protected function mergeOptions($options)
    {
        if (!is_array($options)) {
            return $this->getDefaultOptions();
        }

        return array_merge($this->getDefaultOptions(), $options);
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [];
    }
}