<?php

namespace Botanick\SerializerBundle\Serializer\Config;

use Botanick\SerializerBundle\Exception\ConfigNotFoundException;

class SerializerConfigLoader
{
    /**
     * @var \AppKernel
     */
    private $_appKernel;
    /**
     * @var array
     */
    private $_bundles = [];
    /**
     * @var array
     */
    private $_config = null;

    /**
     * @param \AppKernel $appKernel
     * @param array $bundles
     */
    public function __construct(
        \AppKernel $appKernel,
        array $bundles
    )
    {
        $this->_appKernel = $appKernel;
        $this->_bundles = $bundles;
    }

    /**
     * @return \AppKernel
     */
    protected function getAppKernel()
    {
        return $this->_appKernel;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws ConfigNotFoundException
     */
    public function getConfigFor($name)
    {
        if (is_null($this->_config)) {
            $this->loadConfig();
        }

        if (!isset($this->_config[$name])) {
            throw new ConfigNotFoundException(sprintf('Config for "%s" not found', $name));
        }

        return $this->_config[$name];
    }

    protected function loadConfig()
    {
        $this->_config = [];
    }
}