<?php

namespace Botanick\SerializerBundle\Serializer;

use Botanick\SerializerBundle\Exception\SerializerNotFoundException;
use Botanick\SerializerBundle\Serializer\DataSerializer\DataSerializerInterface;
use Botanick\SerializerBundle\SerializerAwareInterface;
use Botanick\SerializerBundle\SerializerInterface;

class Serializer implements SerializerInterface
{
    /**
     * @var DataSerializerInterface[][]
     */
    protected $_dataSerializers = [];
    /**
     * @var bool
     */
    protected $_dataSerializersNeedSort = false;

    public function serialize($data, $group = self::GROUP_DEFAULT)
    {
        // todo do smth with $config
        $config = null;

        foreach ($this->getDataSerializers() as $dataSerializers) {
            foreach ($dataSerializers as $dataSerializer) {
                if ($dataSerializer->supports($data)) {
                    return $dataSerializer->serialize($data, $group, $config);
                }
            }
        }

        throw new SerializerNotFoundException('No serializers found');
    }

    public function addDataSerializer(DataSerializerInterface $dataSerializer, $priority)
    {
        if ($dataSerializer instanceof SerializerAwareInterface) {
            $dataSerializer->setSerializer($this);
        }

        $this->_dataSerializers[$priority][] = $dataSerializer;
        $this->_dataSerializersNeedSort = true;
    }

    protected function getDataSerializers()
    {
        if ($this->_dataSerializersNeedSort) {
            $this->sortDataSerializers();
            $this->_dataSerializersNeedSort = false;
        }

        return $this->_dataSerializers;
    }

    protected function sortDataSerializers()
    {
        krsort($this->_dataSerializers);
    }
}