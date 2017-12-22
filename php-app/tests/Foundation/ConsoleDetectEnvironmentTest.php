<?php

namespace MapleSyrupGroup\Search\Foundation\Bootstrap;

class ConsoleDetectEnvironmentTest extends \PHPUnit_Framework_TestCase
{

    public function testServiceTestEnvironmentConfigurationIsLoadedInTestMode()
    {
        $detector = new ConsoleDetectEnvironment();
        $filename = $detector->getEnvironmentFilename('sample.env', true);
        $this->assertSame('sample.env' . '.' . ConsoleDetectEnvironment::SERVICE_TEST_KEY, $filename);
    }
}
