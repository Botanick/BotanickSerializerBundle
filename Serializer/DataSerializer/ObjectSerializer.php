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
        $config = $this->getConfig($data, $group);

        return ['a' => 1];
    }

    /**
     * @param \stdClass $data
     * @param string $group
     * @return mixed
     * @throws DataSerializerException
     */
    protected function getConfig($data, $group)
    {
        $className = get_class($data);

        try {
            $config = $this->getConfigLoader()->getConfigFor($className);
        } catch (ConfigNotFoundException $ex) {
            throw new DataSerializerException(
                sprintf(
                    'Cannot serialize an object of class "%s", reason: %s',
                    $className,
                    $ex->getMessage()
                )
            );
        }

        if (isset($config[$group])) {
            return $config[$group];
        } elseif (isset($config[self::GROUP_DEFAULT])) {
            return $config[self::GROUP_DEFAULT];
        }

        throw new DataSerializerException(
            sprintf(
                'Cannot serialize an object of class "%s", neither "%s" nor "%s" group was found',
                $className,
                $group,
                self::GROUP_DEFAULT
            )
        );
    }

    public function supports($data)
    {
        return is_object($data);
    }
}