<?php

namespace Flint\Config;

use Flint\Config\Loader\JsonFileLoader;
use Pimple;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

/**
 * @package Flint
 */
class Configurator
{
    protected $loader;
    protected $cacheDir;
    protected $debug;

    /**
     * @param JsonFileLoader $loader
     */
    public function __construct(JsonFileLoader $loader, $cacheDir, $debug = false)
    {
        $this->loader = $loader;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * @param Pimple $pimple
     * @param string $file
     */
    public function load(Pimple $pimple, $file)
    {
        $metadata = new \ArrayObject;
        $cache = new ConfigCache($this->cacheDir . '/' . crc32($file) . '.php', $this->debug);

        if (!$cache->isFresh()) {
            $parameters = $this->loadFile($file, $metadata);
        }

        if ($this->cacheDir && isset($parameters)) {
            $cache->write('<?php $parameters = ' . var_export($parameters, true) . ';', iterator_to_array($metadata));
        }

        if (!isset($parameters)) {
            require (string) $cache;
        }

        $this->build($pimple, $parameters);
    }

    /**
     * @param string $file
     * @param ArrayObject $metadata
     * @return array
     */
    protected function loadFile($file, \ArrayObject $metadata)
    {
        $parameters = $this->loader->load($file);

        $metadata->append(new FileResource($file));

        if (isset($parameters['@import'])) {
            $parameters = array_replace($this->loadFile($parameters['@import'], $metadata), $parameters);
        }

        return $parameters;
    }

    /**
     * @param Pimple $pimple
     * @param array $parameters
     */
    protected function build(Pimple $pimple, array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $pimple[$key] = $value;
        }
    }
}
