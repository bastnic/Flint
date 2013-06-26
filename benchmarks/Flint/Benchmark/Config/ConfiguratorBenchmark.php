<?php

namespace Flint\Benchmark\Config;

use Flint\Config\Configurator;
use Flint\Config\Loader\JsonFileLoader;
use Flint\Config\Normalizer\ChainNormalizer;
use Flint\Config\Normalizer\PimpleAwareNormalizer;
use Flint\Config\Normalizer\EnvironmentNormalizer;
use Pimple;
use Symfony\Component\Config\FileLocator;

class ConfiguratorBenchmark extends \Athletic\AthleticEvent
{
    public function classSetUp()
    {
        $this->pimple = new Pimple(array(
            'root_dir' => __DIR__,
            'debug' => true,
        ));

        $normalizers = new ChainNormalizer(array(
            new PimpleAwareNormalizer($this->pimple),
            new EnvironmentNormalizer(),
        ));

        $loader = new JsonFileLoader($normalizers, new FileLocator(__DIR__ . '/../Fixtures'));

        // Create configurator and warmup cache
        $this->cached = new Configurator($loader, sys_get_temp_dir());
        $this->cached->configure($this->pimple, 'inherit.json');

        $this->nonCached = new Configurator($loader, null, false);
    }

    public function classTearDown()
    {
        // make sure a previous run isnt interfering
        @unlink(sys_get_temp_dir() . '/' . crc32('inherit.json') . '.php');
    }

    /**
     * @iterations 10000
     */
    public function loadCachedConfigFile()
    {
        $this->cached->configure($this->pimple, 'inherit.json');
    }

    /**
     * @iterations 10000
     */
    public function loadNonCachedConfigFile()
    {
        $this->nonCached->configure($this->pimple, 'inherit.json');
    }

    /**
     * @iterations 10000
     */
    public function loadSimpleNonCachedConfigFile()
    {
        $this->nonCached->configure($this->pimple, 'simple.json');
    }
}
