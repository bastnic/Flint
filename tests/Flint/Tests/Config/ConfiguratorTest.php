<?php

namespace Flint\Tests\Config;

use Flint\Config\Configurator;
use Silex\Application;

class ConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    const CACHE_CONTENT = <<<CONTENT
<?php \$parameters = array (
  'service_parameter' => 'hello',
);
CONTENT;

    public function setUp()
    {
        $this->loader = $this->getMockBuilder('Flint\Config\Loader\JsonFileLoader')->disableOriginalConstructor()
            ->getMock();

        $this->configurator = new Configurator($this->loader);

        $this->app = new Application();
        $this->app['debug'] = true;
        $this->app['config.cache_dir'] = '/var/tmp';

        $this->cacheFile = "/var/tmp/1058386122.php";
    }

    public function tearDown()
    {
        @unlink($this->cacheFile);
    }

    public function testItBuilderApplication()
    {
        $this->app['config.cache_dir'] = null;

        $this->loader->expects($this->once())->method('load')->with($this->equalTo('config.json'))
            ->will($this->returnValue(array('service_parameter' => 'hello')));

        $this->configurator->load($this->app, 'config.json');

        $this->assertEquals('hello', $this->app['service_parameter']);
    }

    public function testItBuildsApplicationWithInheritedConfig()
    {
        $this->loader->expects($this->at(0))->method('load')->with($this->equalTo('config.json'))
            ->will($this->returnValue(array('@import' => 'inherited.json', 'service_parameter' => 'hello')));

        $this->loader->expects($this->at(1))->method('load')->with($this->equalTo('inherited.json'))
            ->will($this->returnValue(array('service_parameter' => 'other thing', 'new_parameter' => true)));

        $this->configurator->load($this->app, 'config.json');

        $this->assertEquals('hello', $this->app['service_parameter']);
        $this->assertEquals(true, $this->app['new_parameter']);
    }

    public function testAFreshCacheSkipsLoader()
    {
        $this->app['debug'] = false;

        // Create a fresh cache
        file_put_contents($this->cacheFile, static::CACHE_CONTENT);

        $this->loader->expects($this->never())->method('load');

        $this->configurator->load($this->app, 'config.json');

        $this->assertEquals('hello', $this->app['service_parameter']);
    }

    public function testStaleCacheWritesFile()
    {
        $this->loader->expects($this->once())->method('load')->with($this->equalTo('config.json'))->will($this->returnValue(array(
            'service_parameter' => 'hello',
        )));

        $this->app['debug'] = false;

        $this->configurator->load($this->app, 'config.json');
    }
}
