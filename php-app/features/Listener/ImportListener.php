<?php

namespace MapleSyrupGroup\Search\Behat\Listener;

use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use MapleSyrupGroup\Search\Behat\Search\TestImport;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Builds the index once before all the tests are run.
 */
final class ImportListener implements EventSubscriberInterface
{
    /**
     * @var TestImport
     */
    private $import;

    /**
     * @param TestImport $import
     */
    public function __construct(TestImport $import)
    {
        $this->import = $import;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            SuiteTested::BEFORE => ['import', 0],
        ];
    }

    public function import()
    {
        static $imported = false;

        if (!$imported) {
            $this->import->import();

            $imported = true;
        }
    }
}
