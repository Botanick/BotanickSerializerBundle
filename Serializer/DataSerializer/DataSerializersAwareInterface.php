<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

interface DataSerializersAwareInterface
{
    /**
     * @param DataSerializerInterface $dataSerializer
     * @param int $priority
     */
    public function addDataSerializer(DataSerializerInterface $dataSerializer, $priority);
}