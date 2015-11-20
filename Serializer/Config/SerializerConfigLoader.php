<?php

namespace Botanick\SerializerBundle\Serializer\Config;

use Botanick\SerializerBundle\Exception\ConfigLoadException;
use Botanick\SerializerBundle\Exception\ConfigNotFoundException;

class SerializerConfigLoader implements SerializerConfigLoaderInterface
{
    /**
     * @var array
     */
    private $_config = null;

    /**
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        $this->setConfig($config);
    }

    public function getConfigFor($name)
    {
        if (is_null($this->_config)) {
            $this->loadConfig();
        }

        if (!array_key_exists($name, $this->_config)) {
            throw new ConfigNotFoundException(sprintf('Config for "%s" not found.', $name));
        }

        return $this->_config[$name];
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config = null)
    {
        $this->_config = $config;
    }

    protected function loadConfig()
    {

    }
}