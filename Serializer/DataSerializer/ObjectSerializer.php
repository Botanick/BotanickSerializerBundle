<?php

namespace Botanick\SerializerBundle\Serializer\DataSerializer;

use Botanick\SerializerBundle\Exception\ConfigNotFoundException;
use Botanick\SerializerBundle\Exception\DataSerializerException;
use Botanick\SerializerBundle\Serializer\Config\SerializerConfigLoader;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ObjectSerializer extends DataSerializer
{
    const PROP_EXTENDS = '$extends$';

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
     * @return mixed
     * @throws DataSerializerException
     */
    public function serialize($data, $group = self::GROUP_DEFAULT, $options = null)
    {
        $config = $this->getConfig($data, $group);

        if ($config === false) {
            return null;
        }

        $result = [];

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor();

        foreach ($config as $prop => $propOptions) {
            if ($propOptions === false) {
                continue;
            }

            try {
                $value = $propertyAccessor->getValue($data, $prop);
            } catch (\Exception $ex) {
                throw new DataSerializerException(
                    sprintf(
                        'Cannot access "%s" property in class "%s". %s',
                        $prop,
                        get_class($data),
                        $ex->getMessage()
                    )
                );
            }

            $result[$prop] = $this->getSerializer()->serialize($value, $group, $propOptions);
        }

        return $result;
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
            $configGroups = $this->getConfigLoader()->getConfigFor($className);
        } catch (ConfigNotFoundException $ex) {
            throw new DataSerializerException(
                sprintf(
                    'Cannot serialize class "%s". %s',
                    $className,
                    $ex->getMessage()
                )
            );
        }

        if (isset($configGroups[$group])) {
            $config = $configGroups[$group];
        } elseif (isset($configGroups[self::GROUP_DEFAULT])) {
            $config = $configGroups[self::GROUP_DEFAULT];
        } else {
            throw new DataSerializerException(
                sprintf(
                    'Cannot serialize class "%s". Neither "%s" nor "%s" group was found.',
                    $className,
                    $group,
                    self::GROUP_DEFAULT
                )
            );
        }

        if (isset($config[self::PROP_EXTENDS])) {
            $extendedGroup = $config[self::PROP_EXTENDS];
            unset($config[self::PROP_EXTENDS]);

            $config = array_merge($this->getConfig($data, $extendedGroup), $config);
        }

        return $config;
    }

    public function supports($data)
    {
        return is_object($data);
    }
}