<?php

namespace Flint\Config\Normalizer;

/**
 * @package Flint
 */
class ChainNormalizer implements NormalizerInterface
{
    protected $normalizers;

    /**
     * @param NormalizerInterface $normalizer
     */
    public function add(NormalizerInterface $normalizer)
    {
        $this->normalizers[] = $normalizer;
    }

    /**
     * {@inheritDoc}
     */
    public function normalize($contents)
    {
        foreach ($this->normalizers as $normalizer) {
            $contents = $normalizer->normalize($contents);
        }

        return $contents;
    }
}
