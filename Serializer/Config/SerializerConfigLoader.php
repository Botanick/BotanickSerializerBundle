<?php

namespace Botanick\SerializerBundle\Serializer\Config;

use Botanick\SerializerBundle\Exception\ConfigLoadException;
use Botanick\SerializerBundle\Exception\ConfigNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

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
     * @return array
     */
    protected function getBundles()
    {
        return $this->_bundles;
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

        if (!array_key_exists($name, $this->_config)) {
            throw new ConfigNotFoundException(sprintf('Config for "%s" not found', $name));
        }

        return $this->_config[$name];
    }

    protected function loadConfig()
    {
        $this->_config = [];

        $finder = new Finder();

        foreach ($this->getBundles() as $bundle) {
            try {
                $dir = $this->getAppKernel()->locateResource($bundle . '/Resources/config/botanick-serializer/');
            } catch (\Exception $ex) {
                throw new ConfigLoadException(sprintf('Unable to find "botanick-serializer" directory in %s bundle', $bundle));
            }

            $finder->files()->in($dir);
            foreach ($finder as $file) {
                $filePath = realpath($file);

                try {
                    $config = Yaml::parse(file_get_contents($filePath));
                } catch (ParseException $ex) {
                    throw new ConfigLoadException(
                        sprintf(
                            'Unable to load config from "%s" (%s bundle): %s',
                            $filePath,
                            $bundle,
                            $ex->getMessage()
                        )
                    );
                }

                $this->_config = array_merge($this->_config, $config);
            }
        }
    }
}