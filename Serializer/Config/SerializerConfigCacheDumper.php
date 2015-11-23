<?php

namespace Botanick\SerializerBundle\Serializer\Config;

class SerializerConfigCacheDumper
{
    /**
     * @param string $class
     * @param array $config
     * @return string
     */
    public function dump($class, $config)
    {
        $serializedConfig = var_export($config, true);

        return <<<EOF
<?php

class {$class} implements Botanick\\SerializerBundle\\Serializer\\Config\\SerializerConfigInterface
{
    public function getConfig()
    {
        return {$serializedConfig};
    }
}
EOF;
    }
}