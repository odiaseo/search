<?php

namespace MapleSyrupGroup\Search\Console\Commands;

use Illuminate\Console\Command;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;
use MapleSyrupGroup\Search\Logs\SymfonyStyleLogger;
use MapleSyrupGroup\Search\Services\Importer\Import;

/**
 * Command to trigger rebuilding the indexes.
 */
class SearchBuildIndex extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds the given Elasticsearch index';

    /**
     * @var Import
     */
    private $importService;

    /**
     * @param Import $importService
     */
    public function __construct(Import $importService)
    {
        $this->signature = sprintf(
            'search:build-index
            {index=%s : Identifier of the index (should be defined in config)}
            {status_id? : Identifier of the index status}
            {--all-domains : Import for all domain IDs, rather than just the current one}
            {--d|domain= : Import for the given domain ID}',
            env('ELASTICSEARCH_INDEX_NAME')
        );

        $this->setImportService($importService);

        parent::__construct();
    }

    /**
     * Execute the command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('all-domains') && $this->option('domain')) {
            throw new \InvalidArgumentException(
                'Specify either the --domain or --all-domains options (not both).',
                ExceptionCodes::CODE_INVALID_ARGUMENT
            );
        }

        $statusId = null !== $this->argument('status_id') ? (int) $this->argument('status_id') : null;
        $indexId  = (string) $this->argument('index');
        $domainId = $this->findDomainId();

        $this->getImportService()->doImport($indexId, $domainId, new SymfonyStyleLogger($this->output), $statusId);

        return 0;
    }

    /**
     * @return int|null
     */
    private function findDomainId()
    {
        if ($this->option('all-domains')) {
            return null;
        }

        return (int) $this->option('domain') ?: env('DOMAIN_ID', null);
    }

    /**
     * @return Import
     */
    public function getImportService()
    {
        return $this->importService;
    }

    /**
     * @param Import $importService
     *
     * @return SearchBuildIndex
     */
    public function setImportService(Import $importService)
    {
        $this->importService = $importService;

        return $this;
    }
}
