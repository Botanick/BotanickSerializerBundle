<?php

namespace Botanick\SerializerBundle\Serializer\Config;

use Botanick\SerializerBundle\Exception\ConfigLoadException;
use Symfony\Component\Finder\Finder;

class SerializerBundlesConfigLoader extends SerializerFilesConfigLoader
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
     * @param \AppKernel $appKernel
     * @param array $bundles
     */
    public function __construct(
        \AppKernel $appKernel,
        array $bundles
    ) {
        parent::__construct();

        $this->_appKernel = $appKernel;
        $this->setBundles($bundles);
    }

    /**
     * @param array $bundles
     */
    protected function setBundles(array $bundles)
    {
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
     * @throws ConfigLoadException
     */
    protected function loadConfig()
    {
        $files = [];

        $finder = new Finder();
        foreach ($this->getBundles() as $bundle) {
            try {
                $dir = $this->getAppKernel()->locateResource($bundle . '/Resources/config/botanick-serializer/');
            } catch (\Exception $ex) {
                throw new ConfigLoadException(sprintf('Unable to find "botanick-serializer" directory in %s bundle.', $bundle));
            }

            $finder->files()->in($dir);
            foreach ($finder as $file) {
                $files[] = $file;
            }
        }

        parent::setFiles($files);
        parent::loadConfig();
    }
}