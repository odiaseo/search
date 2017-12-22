<?php

namespace MapleSyrupGroup\Search\Providers;

use Elastica\Query;
use Elastica\Query\Term;
use Elastica\Search;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\QCommon\Enum\DomainEnum;
use MapleSyrupGroup\Search\Models\SearchableModelMapper;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use MapleSyrupGroup\Search\Services\Client\SearchClient;
use MapleSyrupGroup\Search\Services\Importer\Documents\DocumentsFromSearchModel;
use MapleSyrupGroup\Search\Services\Importer\Documents\Hydrators\ElasticaDocumentHydrator;
use MapleSyrupGroup\Search\Services\Importer\Documents\Validators\DocumentHydrationValidator;
use MapleSyrupGroup\Search\Services\Importer\ElasticaImporter;
use MapleSyrupGroup\Search\Services\Importer\Import;
use MapleSyrupGroup\Search\Services\Importer\IndexMapping\ElasticaMapping;
use MapleSyrupGroup\Search\Services\Importer\Types\ClassTypeBuilder;
use MapleSyrupGroup\Search\Services\Importer\Types\StaticTypeBuilder;
use MapleSyrupGroup\Search\Services\IndexBuilder\ElasticaIndexBuilder;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\IndexStatusTracker;

/**
 * Build the merchant search provider service.
 */
class MerchantImporterServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(ElasticaImporter::class, function (Application $app) {

            /** @var BusinessEventLogger $logger */
            /* @var SearchClient | \Elastica\Client $elasticaClient */
            /* @var IndexStatusTracker $statusTracker */

            $logger      = $app->make(BusinessEventLogger::class);
            $config      = $app->make('config');
            $modelParams = ['stopWords' => $config->get('elasticsearch.stopWords')];
            $indexes     = $config->get('elasticsearch.indexes');
            $bufferSize  = $config->get('elasticsearch.client.bulk_max_size');
            $retryCount  = $config->get('indexbuilder.retries');

            $elasticaClient = $app->make(ElasticaSearchClient::class);
            $statusTracker  = $app->make(IndexStatusTracker::class);
            $indexBuilders  = [];

            foreach ($indexes as $index => $indexConfig) {
                $types              = [];
                $documentPopulators = [];

                if (isset($indexConfig['types'])) {
                    foreach ($indexConfig['types'] as $type => $typeConfig) {
                        $types[$type] = new StaticTypeBuilder($type, $typeConfig, new ElasticaMapping());
                    }

                    $indexBuilders[$index] = new ElasticaIndexBuilder(
                        $elasticaClient,
                        $indexConfig['settings'],
                        $types,
                        $documentPopulators,
                        $logger
                    );
                }
            }

            foreach ($indexes as $index => $indexConfig) {
                if (isset($indexConfig['class_types'])) {
                    foreach ($indexConfig['class_types'] as $typeId => $searchableClassNames) {
                        foreach ($searchableClassNames as $domainId => $searchableClassName) {
                            // Set mapper class for generating index/type mapping
                            $mapperClassName = $indexConfig['mapping_types'][$typeId][$domainId];

                            // set global domain id
                            // required for inter service communication
                            if (DomainEnum::DOMAIN_ID_SHOOP == $domainId) {
                                config([
                                    'domain_id' => $domainId,
                                ]);
                            }

                            // Bind mapper class to searchable model based on domain id
                            $this->app->when($searchableClassName)
                                ->needs(SearchableModelMapper::class)
                                ->give($mapperClassName);

                            $searchableClass   = $app->make($searchableClassName, $modelParams);
                            $searcher          = new Search($elasticaClient);

                            $documentPopulator = new ElasticaDocumentHydrator(
                                $domainId,
                                $typeId,
                                new DocumentHydrationValidator($searcher, $logger, new Query(), new Term()),
                                $logger,
                                new DocumentsFromSearchModel($searchableClass, $logger, $bufferSize)
                            );

                            $indexBuilders[$index . '_' . $domainId] = new ElasticaIndexBuilder(
                                $elasticaClient,
                                $indexConfig['settings'],
                                [new ClassTypeBuilder($typeId, $searchableClass, new ElasticaMapping())],
                                [$documentPopulator],
                                $logger
                            );
                        }
                    }
                }
            }

            return new ElasticaImporter($indexBuilders, $statusTracker, $logger, $retryCount);
        });
        $this->app->bind(Import::class, ElasticaImporter::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Import::class,
            ElasticaImporter::class,
        ];
    }
}
