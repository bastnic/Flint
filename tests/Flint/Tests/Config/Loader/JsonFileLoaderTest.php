<?php

namespace Flint\Tests\Config\Loader;

use Flint\Config\Loader\JsonFileLoader;
use Symfony\Component\Config\FileLocator;

class JsonFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $paths = array(__DIR__ . '/../../Fixtures');

        $normalizer = $this->getMock('Flint\Config\Normalizer\NormalizerInterface');
        $normalizer->expects($this->any())->method('normalize')->will($this->returnCallback(function ($args) {
            return $args;
        }));

        $this->loader = new JsonFileLoader(new FileLocator($paths), $normalizer);
    }

    public function testItLoadsAsJsonFile()
    {
        $this->assertEquals(array('service_parameter' => 'hello'), $this->loader->load('config.json'));
    }

    public function testItThrowsExceptionIfFileIsntJson()
    {
        $this->setExpectedException('InvalidArgumentException', 'Format for file "unsupported.ini" is not supported.');

        $this->loader->load('unsupported.ini');
    }
}
