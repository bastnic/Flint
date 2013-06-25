<?php

namespace Flint\Config\Normalizer;

/**
 * @package Flint
 */
class PimpleAwareNormalizer extends \Flint\PimpleAware implements NormalizerInterface
{
    /**
     * @param string $contents
     * @return string
     */
    public function normalize($contents)
    {
        $pimple = $this->pimple;

        $contents = preg_replace_callback('/%([A-Za-z0-9_.]+)%/', function ($matches) use ($pimple) {
            $value = $pimple[$matches[1]];

            return is_bool($value) ? ($value ? 'true' : 'false') : $value;
        }, $contents);

        return preg_replace_callback('/#([A-Za-z0-9_]+)#/', function ($matches) use ($pimple) {
            return getenv($matches[1]) ?: $matches[1];
        }, $contents);
    }
}
