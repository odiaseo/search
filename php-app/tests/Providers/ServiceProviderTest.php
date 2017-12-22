<?php

namespace MapleSyrupGroup\Search\Providers;

use MapleSyrupGroup\QCommon\Guzzle;
use MapleSyrupGroup\Quidco\ApiClient\ClientInterface;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Models\Merchants\Filters\CategoryNameFilter;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantNameFilter;
use MapleSyrupGroup\Search\Models\Merchants\Filters\StopWordFilter;
use MapleSyrupGroup\Search\Providers\Filters\CategoryNameFilterProvider;
use MapleSyrupGroup\Search\Providers\Filters\MerchantNameFilterProvider;
use MapleSyrupGroup\Search\Providers\Filters\StopWordFilterProvider;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use MapleSyrupGroup\Search\Services\Importer\Import;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\IndexStatusTracker;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchants;
use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Search\Test\Application;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    use Application;

    public function setUp()
    {
        $this->initApplication();
    }

    /**
     * @dataProvider configDataProvider
     *
     * @param string $providerClass
     * @param string $expectedClass
     */
    public function testTheProviderProvidesTheExpectedClass($providerClass, $expectedClass)
    {
        $provider = new $providerClass($this->app);
        $provider->register();

        $data = $provider->provides();

        $this->assertInternalType('array', $data);

        if (count($data)) {
            $this->assertSame($expectedClass, $data[0]);
        }
    }

    /**
     * @dataProvider configDataProvider
     *
     * @param string $providerClass
     * @param string $expectedClass
     * @param array $configOverride
     */
    public function testThatAnInstanceOfTheProvidedServiceCanBeCreated($providerClass, $expectedClass, $configOverride)
    {
        config($configOverride);

        $provider = new $providerClass($this->app);
        $provider->register();

        $object = $this->app->make($expectedClass);

        $this->assertInstanceOf($expectedClass, $object);
    }

    public function configDataProvider()
    {
        return [
            [IndexStatusTrackerProvider::class, IndexStatusTracker::class, []],
            [
                BusinessEventLoggerProvider::class,
                BusinessEventLogger::class,
                [
                    'papertrail.syslog.system-name' => true,
                    'papertrail.syslog.socket'      => true,
                ],
            ],
            [MerchantImporterServiceProvider::class, Import::class, []],
            [GuzzleProvider::class, Guzzle::class, []],

            [ElasticSearchClientProvider::class, ElasticaSearchClient::class, []],
            [MerchantSearchServiceProvider::class, Search::class, []],
            [MerchantsServiceProvider::class, Merchants::class, []],
            [QuidcoApiServiceProvider::class, ClientInterface::class, []],
            [BusinessEventLoggerProvider::class, BusinessEventLogger::class, []],
            [CategoryMerchantSearchServiceProvider::class, Search::class, []],
            [MerchantFilterAggregateProvider::class, MerchantFilterAggregate::class, []],
            [CategoryNameFilterProvider::class, CategoryNameFilter::class, []],
            [MerchantNameFilterProvider::class, MerchantNameFilter::class, []],
            [StopWordFilterProvider::class, StopWordFilter::class, []],
        ];
    }


    public function testApiRequestProviderProvidesRequestInstance()
    {
        $provider = new ApiRequestProvider($this->app);
        $this->assertContains(ApiRequest::class, $provider->provides());
    }

    public function tearDown()
    {
        $this->destroyApplication();
    }
}
