<?php

namespace Botanick\SerializerBundle\Serializer\Config;

use AppKernel;
use Botanick\SerializerBundle\Exception\ConfigLoadException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Finder\Finder;

class SerializerBundlesConfigLoader extends SerializerFilesConfigLoader
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
     * @var SerializerConfigCacheDumper
     */
    private $_dumper;

    /**
     * @param AppKernel $appKernel
     * @param array $bundles
     * @param SerializerConfigCacheDumper $dumper
     */
    public function __construct(AppKernel $appKernel, array $bundles = [], SerializerConfigCacheDumper $dumper) {
        parent::__construct();

        $this->_appKernel = $appKernel;
        $this->setBundles($bundles);
        $this->_dumper = $dumper;
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
     * @return SerializerConfigCacheDumper
     */
    protected function getDumper()
    {
        return $this->_dumper;
    }

    /**
     * @throws ConfigLoadException
     */
    protected function loadConfig()
    {
        $class = sprintf(
            '%s%sBotanickSerializerConfig',
            $this->getAppKernel()->getName(),
            ucfirst($this->getAppKernel()->getEnvironment())
        );
        $cache = new ConfigCache(
            sprintf(
                '%s/%s.php',
                $this->getAppKernel()->getCacheDir(),
                $class
            ),
            $this->getAppKernel()->isDebug()
        );

        if (!$cache->isFresh()) {
            $files = [];
            $resources = [];

            $finder = new Finder();
            foreach ($this->getBundles() as $bundle) {
                try {
                    $dir = $this->getAppKernel()->locateResource($bundle . '/Resources/config/botanick-serializer/');
                } catch (\Exception $ex) {
                    throw new ConfigLoadException(sprintf('Unable to find "botanick-serializer" directory in %s bundle.', $bundle));
                }

                $resources[] = new DirectoryResource($dir);
                $finder->files()->in($dir);
                foreach ($finder as $file) {
                    $files[] = $file;
                }
            }

            parent::setFiles($files);
            parent::loadConfig();

            $cache->write($this->getDumper()->dump($class, $this->getConfig()), $resources);

            return;
        }

        require_once $cache;
        /** @var SerializerConfigInterface $config */
        $config = new $class();
        $this->setConfig($config->getConfig());
    }
}