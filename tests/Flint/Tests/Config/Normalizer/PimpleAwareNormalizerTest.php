<?php

namespace Flint\Tests\Config\Normalizer;

use Flint\Config\Normalizer\PimpleAwareNormalizer;

class PimpleAwareNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testItReplacesPlaceHolders()
    {
        $pimple = new \Pimple(array('service_parameter' => 'hello'));

        $normalizer = new PimpleAwareNormalizer;
        $normalizer->setPimple($pimple);

        $this->assertEquals('hello', $normalizer->normalize('%service_parameter%'));
    }
}
