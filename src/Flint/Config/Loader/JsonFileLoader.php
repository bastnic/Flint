<?php

namespace Flint\Config\Loader;

use Flint\Config\Normalizer\ChainNormalizer;
use Symfony\Component\Config\FileLocator;

/**
 * @package Flint
 */
class JsonFileLoader
{
    protected $locator;
    protected $normalizer;

    /**
     * @param FileLocator $locator
     * @param ChainNormalizer $normalizer
     */
    public function __construct(FileLocator $locator, ChainNormalizer $normalizer)
    {
        $this->locator = $locator;
        $this->normalizer = $normalizer;
    }

    /**
     * @param string $file
     * @return array
     */
    public function load($file)
    {
        if (!$this->supports($file)) {
            throw new \InvalidArgumentException('Format for file "' . $file . '" is not supported.');
        } 

        $contents = file_get_contents($this->locator->locate($file));

        return json_decode($this->normalizer->normalize($contents), true);
    }

    /**
     * @param string $file
     * @return boolean
     */
    public function supports($file)
    {
        return 'json' === pathinfo($file, PATHINFO_EXTENSION);
    }
}
