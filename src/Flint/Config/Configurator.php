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
        $metadata = array(new FileResource($file));
        $cache = new ConfigCache($this->cacheDir . '/' . crc32($file) . '.php', $this->debug);

        if (!$fresh = $cache->isFresh()) {
            $parameters = $this->loader->load($file);

            if (isset($parameters['@import'])) {
                $parameters = array_replace($this->loader->load($parameters['@import']), $parameters);
                $metadata[] = new FileResource($parameters['@import']);
            }
        }

        if ($this->cacheDir && !$fresh) {
            $cache->write('<?php $parameters = ' . var_export($parameters, true) . ';', $metadata);
        }

        if (!isset($parameters)) {
            require (string) $cache;
        }

        $this->build($pimple, $parameters);
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
