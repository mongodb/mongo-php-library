<?php

namespace MongoDB\Tests;

use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;

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

        $cursor = $this->manager->executeCommand($databaseName, new Command(array('count' => $collectionName)));

        $document = current($cursor->toArray());
        $this->assertArrayHasKey('n', $document);
        $this->assertEquals($count, $document['n']);
    }

    protected function assertCommandSucceeded(Cursor $cursor)
    {
        $document = current($cursor->toArray());
        $this->assertArrayHasKey('ok', $document);
        $this->assertEquals(1, $document['ok']);
    }

    protected function getServerVersion(ReadPreference $readPreference = null)
    {
        $cursor = $this->manager->executeCommand(
            $this->getDatabaseName(),
            new Command(array('buildInfo' => 1)),
            $readPreference ?: new ReadPreference(ReadPreference::RP_PRIMARY)
        );

        $document = current($cursor->toArray());

        return $document['version'];
    }
}
