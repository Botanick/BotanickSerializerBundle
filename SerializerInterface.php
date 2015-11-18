<?php

namespace Botanick\SerializerBundle;

interface SerializerInterface
{
    const GROUP_DEFAULT = 'default';

    /**
     * @param mixed $data
     * @param string $group
     * @return mixed
     */
    public function serialize($data, $group = self::GROUP_DEFAULT);
}