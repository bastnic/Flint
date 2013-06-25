<?php

namespace Flint\Benchmark\Config;

use Flint\Config\Configurator;
use Flint\Config\Loader\JsonFileLoader;
use Flint\Config\Normalizer\ChainNormalizer;
use Pimple;
use Symfony\Component\Config\FileLocator;

class ConfiguratorBenchmark extends \Athletic\AthleticEvent
{
    public function setUp()
    {
        $locator = new FileLocator(array(
            __DIR__ . '/../../../../tests/Flint/Tests/Fixtures',
        ));

        $this->pimple = new Pimple;

        // make sure a previous run isnt interfering
        @unlink(sys_get_temp_dir() . '/' . crc32('config.json') . '.php');

        // Create configurator and warmup cache
        $this->configurator = new Configurator(new JsonFileLoader(new ChainNormalizer, $locator), sys_get_temp_dir());
        $this->configurator->load($this->pimple, 'config.json');
    }

    /**
     * @iterations 10000
     */
    public function loadConfigFile()
    {
        $this->configurator->load($this->pimple, 'config.json');
    }
}
