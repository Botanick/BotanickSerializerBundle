<?php

namespace Botanick\SerializerBundle\Serializer\Config;

use Botanick\SerializerBundle\Exception\ConfigLoadException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class SerializerFilesConfigLoader extends SerializerArrayConfigLoader
{
    /**
     * @var array
     */
    private $_files = [];

    /**
     * @var SerializerConfigCache
     */
    private $_cache;

    /**
     * @param array $files
     * @param SerializerConfigCache $cache
     */
    public function __construct(array $files = [], SerializerConfigCache $cache = null)
    {
        parent::__construct();

        $this->setFiles($files);
        $this->_cache = $cache;
    }

    /**
     * @param array $files
     */
    public function setFiles(array $files)
    {
        $this->_files = $files;
    }

    /**
     * @return array
     */
    protected function getFiles()
    {
        return $this->_files;
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
        return 'files';
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
            $this->getFiles(),
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
        $config = [];

        foreach ($this->getFiles() as $file) {
            if (false === $filePath = realpath($file)) {
                throw new ConfigLoadException(
                    sprintf(
                        'Unable to load config from "%s". File not found.',
                        $file
                    )
                );
            }
            if (!is_readable($filePath)) {
                throw new ConfigLoadException(
                    sprintf(
                        'Unable to load config from "%s". File is not readable.',
                        $file
                    )
                );
            }

            try {
                $yaml = Yaml::parse(file_get_contents($filePath));
            } catch (ParseException $ex) {
                throw new ConfigLoadException(
                    sprintf(
                        'Unable to load config from "%s". %s',
                        $filePath,
                        $ex->getMessage()
                    )
                );
            }

            $config = array_merge($config, $yaml);
        }

        parent::setConfig($config);

        return [
            $this->getConfig(),
            $this->getFiles()
        ];
    }
}