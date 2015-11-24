<?php

namespace Botanick\SerializerBundle\Serializer\Config;

use Botanick\SerializerBundle\Exception\ConfigLoadException;
use Symfony\Component\Finder\Finder;

class SerializerDirsConfigLoader extends SerializerFilesConfigLoader
{
    /**
     * @var array
     */
    private $_dirs = [];

    /**
     * @param array $dirs
     */
    public function __construct(array $dirs = [])
    {
        parent::__construct();

        $this->setDirs($dirs);
    }

    /**
     * @param array $dirs
     */
    public function setDirs(array $dirs)
    {
        $this->_dirs = $dirs;
    }

    /**
     * @return array
     */
    protected function getDirs()
    {
        return $this->_dirs;
    }

    /**
     * @throws ConfigLoadException
     */
    protected function loadConfig()
    {
        $files = [];

        $finder = new Finder();
        foreach ($this->getDirs() as $dir) {
            if (false === $dirPath = realpath($dir)) {
                throw new ConfigLoadException(
                    sprintf(
                        'Unable to load config from "%s". Directory not found.',
                        $dir
                    )
                );
            }
            if (!is_dir($dirPath)) {
                throw new ConfigLoadException(
                    sprintf(
                        'Unable to load config from "%s". Not a directory.',
                        $dir
                    )
                );
            }
            if (!is_readable($dirPath)) {
                throw new ConfigLoadException(
                    sprintf(
                        'Unable to load config from "%s". Directory is not readable.',
                        $dir
                    )
                );
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