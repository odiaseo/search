<?php

namespace MapleSyrupGroup\Search\Console;

use MapleSyrupGroup\Search\Console\Stub\SearchBuildIndexStub;
use MapleSyrupGroup\Search\Services\Importer\Import;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\StyleInterface;

class SearchIndexBuilderTest extends \PHPUnit_Framework_TestCase
{
    const DOMAIN_ID = 1;

    public function testIndexBuilderCanExecuteImportCommand()
    {
        $import = $this->prophesize(Import::class);
        $input  = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(StyleInterface::class);

        $import->doImport(Argument::cetera())->shouldBeCalled();
        $command = new SearchBuildIndexStub($import->reveal(), $input->reveal(), $output->reveal());

        $command->handle();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIndexBuilderThrowsExceptionWhenDomainAndAllDomainsAreSpecified()
    {
        $import = $this->prophesize(Import::class);
        $input  = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(StyleInterface::class);

        $input->getOption('domain')->willReturn(self::DOMAIN_ID);
        $input->getOption('all-domains')->willReturn(true);

        $command = new SearchBuildIndexStub($import->reveal(), $input->reveal(), $output->reveal());

        $command->handle();
    }

    public function testIndexBuilderCanBuildIndexForAllDomains()
    {
        $import = $this->prophesize(Import::class);
        $input  = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(StyleInterface::class);

        $input->getOption('domain')->willReturn(null);
        $input->getOption('all-domains')->willReturn(true);
        $input->getArgument('index')->willReturn(true);

        $input->getArgument('status_id')->shouldBeCalled();
        $import->doImport(Argument::cetera())->shouldBeCalled();

        $command = new SearchBuildIndexStub($import->reveal(), $input->reveal(), $output->reveal());

        $command->handle();
    }

    public function testIndexBuilderCanBuildIndexForSpecifiedDomain()
    {
        $import = $this->prophesize(Import::class);
        $input  = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(StyleInterface::class);

        $input->getOption('domain')->willReturn(self::DOMAIN_ID);
        $input->getOption('all-domains')->willReturn(null);
        $input->getArgument('index')->willReturn(true);

        $input->getArgument('status_id')->shouldBeCalled();
        $import->doImport(Argument::cetera())->shouldBeCalled();

        $command = new SearchBuildIndexStub($import->reveal(), $input->reveal(), $output->reveal());

        $command->handle();
    }
}
