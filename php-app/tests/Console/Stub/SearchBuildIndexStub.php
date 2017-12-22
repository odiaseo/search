<?php

namespace MapleSyrupGroup\Search\Console\Stub;

use MapleSyrupGroup\Search\Console\Commands\SearchBuildIndex;
use MapleSyrupGroup\Search\Services\Importer\Import;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\StyleInterface;

class SearchBuildIndexStub extends SearchBuildIndex
{
    public function __construct(Import $importService, InputInterface $input, StyleInterface $output)
    {
        parent::__construct($importService);

        $this->input  = $input;
        $this->output = $output;
    }
}
