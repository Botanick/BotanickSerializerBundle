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
     * @param array $files
     */
    public function __construct(array $files = [])
    {
        parent::__construct();

        $this->setFiles($files);
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
     * @throws ConfigLoadException
     */
    protected function loadConfig()
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
    }
}