<?php

namespace Flint\Provider;

use Flint\Config\Configurator;
use Flint\Config\Loader\JsonFileLoader;
use Flint\Config\Normalizer\PimpleAwareNormalizer;
use Flint\Config\Normalizer\ChainNormalizer;
use Silex\Application;
use Symfony\Component\Config\FileLocator;

/**
 * @package Flint
 */
class ConfigServiceProvider implements \Silex\ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        $app['config.cache_dir'] = '';

        $app['config.paths'] = function (Application $app) {
            return array($app['root_dir'] . '/config', $app['root_dir']);
        };

        $app['config.locator'] = $app->share(function (Application $app) {
            return new FileLocator($app['config.paths']);
        });

        $app['config.normalizer'] = $app->share(function (Application $app) {
            $pimpleAware = new PimpleAwareNormalizer;
            $pimpleAware->setPimple($app);

            $normalizer = new ChainNormalizer;
            $normalizer->add($pimpleAware);

            return $normalizer;
        });

        $app['config.json_file_loader'] = $app->share(function (Application $app) {
            return new JsonFileLoader($app['config.locator'], $app['config.normalizer']);
        });

        $app['configurator'] = $app->share(function (Application $app) {
            return new Configurator($app['config.json_file_loader']);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
