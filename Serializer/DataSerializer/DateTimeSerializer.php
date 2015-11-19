<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

class DateTimeSerializer extends DataSerializer
{
    /**
     * @param \DateTime $data
     * @param string $group
     * @param mixed $options
     * @return int|string
     */
    public function serialize($data, $group = self::GROUP_DEFAULT, $options = null)
    {
        if (!is_array($options) || !isset($options['format'])) {
            return $data->getTimestamp();
        }

        return $data->format($options['format']);
    }

    public function supports($data)
    {
        return $data instanceof \DateTime;
    }
}