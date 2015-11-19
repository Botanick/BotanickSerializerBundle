<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

use Botanick\SerializerBundle\Exception\ConfigNotFoundException;
use Botanick\SerializerBundle\Exception\DataSerializerException;
use Botanick\SerializerBundle\Serializer\Config\SerializerConfigLoader;

class ObjectSerializer extends DataSerializer
{
    /**
     * @var SerializerConfigLoader
     */
    private $_configLoader;

    /**
     * @param SerializerConfigLoader $configLoader
     */
    public function setConfigLoader(SerializerConfigLoader $configLoader)
    {
        $this->_configLoader = $configLoader;
    }

    /**
     * @return SerializerConfigLoader
     */
    protected function getConfigLoader()
    {
        return $this->_configLoader;
    }

    /**
     * @param \stdClass $data
     * @param string $group
     * @param mixed $options
     * @return array
     * @throws DataSerializerException
     */
    public function serialize($data, $group = self::GROUP_DEFAULT, $options = null)
    {
        try {
            $config = $this->getConfigLoader()->getConfigFor(get_class($data));
        } catch (ConfigNotFoundException $ex) {
            throw new DataSerializerException(
                sprintf(
                    'Cannot serialize an object of class "%s", reason: %s',
                    get_class($data),
                    $ex->getMessage()
                )
            );
        }

        return ['a' => 1];
    }

    public function supports($data)
    {
        return is_object($data);
    }
}