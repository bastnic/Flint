<?php

namespace Flint\Config;

use Flint\Config\Loader\JsonFileLoader;
use Silex\Application;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

/**
 * @package Flint
 */
class Configurator
{
    protected $loader;

    /**
     * @param JsonFileLoader $loader
     */
    public function __construct(JsonFileLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param Application $app
     * @param string $file
     */
    public function load(Application $app, $file)
    {
        $cacheFile = $app['config.cache_dir'] . '/' . crc32($file) . '.php';
        $cache = new ConfigCache($cacheFile, false);
        $fresh = $cache->isFresh();

        if (!$fresh) {
            $parameters = $this->loader->load($file);
        }

        if (!$app['config.cache_dir']) {
            $this->build($app, $parameters);

            return;
        }

        if (!$fresh) {
            $metadata = array(new FileResource($file));
            $cache->write('<?php $parameters = ' . var_export($parameters, true) . ';', $metadata);
        }

        require $cacheFile;

        $this->build($app, $parameters);
    }

    /**
     * @param Application $app
     * @param array $parameters
     */
    protected function build(Application $app, array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $app[$key] = $value;
        }
    }
}
