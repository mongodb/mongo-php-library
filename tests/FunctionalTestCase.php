<?php

namespace MongoDB\Tests;

use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Cursor;

abstract class FunctionalTestCase extends TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->manager = new Manager($this->getUri());
    }

    public function assertCollectionCount($namespace, $count)
    {
        list($databaseName, $collectionName) = explode('.', $namespace, 2);

        $cursor = $this->manager->executeCommand($databaseName, new Command(array('count' => $collectionName)));

        $document = current($cursor->toArray());
        $this->assertArrayHasKey('n', $document);
        $this->assertEquals($count, $document['n']);
    }

    public function assertCommandSucceeded(Cursor $cursor)
    {
        $document = current($cursor->toArray());
        $this->assertArrayHasKey('ok', $document);
        $this->assertEquals(1, $document['ok']);
    }
}
