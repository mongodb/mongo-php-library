<?php

namespace MongoDB\Tests;

use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use stdClass;
use Traversable;

abstract class FunctionalTestCase extends TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->manager = new Manager($this->getUri());
    }

    protected function assertCollectionCount($namespace, $count)
    {
        list($databaseName, $collectionName) = explode('.', $namespace, 2);

        $cursor = $this->manager->executeCommand($databaseName, new Command(['count' => $collectionName]));
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $document = current($cursor->toArray());

        $this->assertArrayHasKey('n', $document);
        $this->assertEquals($count, $document['n']);
    }

    protected function assertCommandSucceeded($document)
    {
        $document = is_object($document) ? (array) $document : $document;

        $this->assertArrayHasKey('ok', $document);
        $this->assertEquals(1, $document['ok']);
    }

    protected function assertSameDocument($expectedDocument, $actualDocument)
    {
        $this->assertEquals(
            is_object($expectedDocument) ? (array) $expectedDocument : $expectedDocument,
            is_object($actualDocument) ? (array) $actualDocument : $actualDocument
        );
    }

    protected function assertSameDocuments(array $expectedDocuments, $actualDocuments)
    {
        if ($actualDocuments instanceof Traversable) {
            $actualDocuments = iterator_to_array($actualDocuments);
        }

        if ( ! is_array($actualDocuments)) {
            throw new InvalidArgumentException('$actualDocuments is not an array or Traversable');
        }

        $normalizeRootDocuments = function($document) {
            return is_object($document) ? (array) $document : $document;
        };

        $this->assertEquals(
            array_map($normalizeRootDocuments, $expectedDocuments),
            array_map($normalizeRootDocuments, $actualDocuments)
        );
    }

    protected function getPrimaryServer()
    {
        return $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));
    }

    protected function getServerVersion(ReadPreference $readPreference = null)
    {
        $cursor = $this->manager->executeCommand(
            $this->getDatabaseName(),
            new Command(['buildInfo' => 1]),
            $readPreference ?: new ReadPreference(ReadPreference::RP_PRIMARY)
        );

        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $document = current($cursor->toArray());

        return $document['version'];
    }
}
