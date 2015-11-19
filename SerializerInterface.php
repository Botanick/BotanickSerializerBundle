<?php

namespace Botanick\SerializerBundle;

use Botanick\SerializerBundle\Exception\DataSerializerException;
use Botanick\SerializerBundle\Exception\SerializerNotFoundException;

interface SerializerInterface
{
    const GROUP_DEFAULT = 'default';

    /**
     * @param mixed $data
     * @param string $group
     * @param mixed $options
     * @return mixed
     * @throws SerializerNotFoundException
     * @throws DataSerializerException
     */
    public function serialize($data, $group = self::GROUP_DEFAULT, $options = null);
}