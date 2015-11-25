<?php

namespace Botanick\SerializerBundle\Serializer\Config;

use AppKernel;
use Botanick\Serializer\Exception\ConfigLoadException;
use Botanick\Serializer\Serializer\Config\SerializerConfigCache;
use Botanick\Serializer\Serializer\Config\SerializerDirsConfigLoader;

class SerializerBundlesConfigLoader extends SerializerDirsConfigLoader
{
    /**
     * @var AppKernel
     */
    private $_appKernel;
    /**
     * @var array
     */
    private $_bundles = [];
    /**
     * @var SerializerConfigCache
     */
    private $_cache;

    /**
     * @param AppKernel $appKernel
     * @param array $bundles
     * @param SerializerConfigCache $cache
     */
    public function __construct(AppKernel $appKernel, array $bundles = [], SerializerConfigCache $cache = null)
    {
        parent::__construct();

        $this->_appKernel = $appKernel;
        $this->setBundles($bundles);
        $this->_cache = $cache;
    }

    /**
     * @param array $bundles
     */
    protected function setBundles(array $bundles)
    {
        $this->_bundles = $bundles;
    }

    /**
     * @return AppKernel
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
     * @return SerializerConfigCache
     */
    private function getCache()
    {
        return $this->_cache;
    }

    /**
     * @return string
     */
    private function getCacheType()
    {
        return 'bundles';
    }

    /**
     * @throws ConfigLoadException
     */
    protected function loadConfig()
    {
        if (!$this->getCache()) {
            $this->loadConfigInternal();

            return;
        }

        $config = $this->getCache()->getCachedConfig(
            $this->getCacheType(),
            $this->getBundles(),
            function () {
                return $this->loadConfigInternal();
            }
        );
        $this->setConfig($config);
    }

    /**
     * @return array
     * @throws ConfigLoadException
     */
    private function loadConfigInternal()
    {
        $dirs = [];

        foreach ($this->getBundles() as $bundle) {
            try {
                $dir = $this->getAppKernel()->locateResource($bundle . '/Resources/config/botanick-serializer/');
            } catch (\Exception $ex) {
                throw new ConfigLoadException(
                    sprintf('Unable to find "botanick-serializer" directory in %s bundle.', $bundle),
                    0,
                    $ex
                );
            }

            $dirs[] = $dir;
        }

        parent::setDirs($dirs);
        parent::loadConfig();

        return [
            $this->getConfig(),
            $dirs
        ];
    }
}