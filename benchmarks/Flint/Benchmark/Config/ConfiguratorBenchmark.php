<?php

namespace Flint\Benchmark\Config;

use Flint\Config\Configurator;
use Flint\Config\Normalizer\ChainNormalizer;
use Flint\Config\Loader\JsonFileLoader;
use Silex\Application;
use Symfony\Component\Config\FileLocator;

class ConfiguratorBenchmark extends \Athletic\AthleticEvent
{
    public function setUp()
    {
        $locator = new FileLocator(array(
            __DIR__ . '/../../../../tests/Flint/Tests/Fixtures',
        ));

        $this->app = new Application(array(
            'debug'            => false,
            'config.cache_dir' => sys_get_temp_dir(),
        ));

        // Create configurator and warmup cache
        $this->configurator = new Configurator(new JsonFileLoader($locator, new ChainNormalizer()));
        $this->configurator->load($this->app, 'config.json');
    }

    /**
     * @iterations 10000
     */
    public function loadConfigFile()
    {
        $this->configurator->load($this->app, 'config.json');
    }
}
