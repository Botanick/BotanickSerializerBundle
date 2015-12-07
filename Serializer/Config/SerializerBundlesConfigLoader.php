<?php

namespace Botanick\SerializerBundle\Serializer\Config;

use Botanick\Serializer\Exception\ConfigLoadException;
use Botanick\Serializer\Serializer\Config\SerializerConfigCache;
use Botanick\Serializer\Serializer\Config\SerializerDirsConfigLoader;
use Symfony\Component\Config\FileLocatorInterface;

class SerializerBundlesConfigLoader extends SerializerDirsConfigLoader
{
    /**
     * @var FileLocatorInterface
     */
    private $_fileLocator;
    /**
     * @var array
     */
    private $_bundles = array();
    /**
     * @var SerializerConfigCache
     */
    private $_cache;

    /**
     * @param FileLocatorInterface $fileLocator
     * @param array $bundles
     * @param SerializerConfigCache $cache
     */
    public function __construct(FileLocatorInterface $fileLocator, array $bundles = array(), SerializerConfigCache $cache = null)
    {
        parent::__construct();

        $this->_fileLocator = $fileLocator;
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
     * @return FileLocatorInterface
     */
    protected function getFileLocator()
    {
        return $this->_fileLocator;
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
        $dirs = array();

        foreach ($this->getBundles() as $bundle) {
            try {
                $locatedDirs = $this->getFileLocator()->locate($bundle . '/Resources/config/botanick-serializer/', null, false);
            } catch (\InvalidArgumentException $ex) {
                throw new ConfigLoadException(
                    sprintf('Unable to find "botanick-serializer" directory in %s bundle.', $bundle),
                    0,
                    $ex
                );
            }

            $dirs = array_merge($dirs, array_reverse($locatedDirs));
        }

        parent::setDirs($dirs);
        parent::loadConfig();

        return array(
            $this->getConfig(),
            $dirs
        );
    }
}