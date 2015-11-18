<?php

namespace Botanick\SerializerBundle;

interface SerializerAwareInterface
{
    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer);
}