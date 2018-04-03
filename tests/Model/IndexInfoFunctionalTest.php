<?php

namespace MongoDB\Tests\Model;

use MongoDB\Collection;
use MongoDB\Tests\FunctionalTestCase;

class IndexInfoFunctionalTest extends FunctionalTestCase
{
    private $collection;

    public function setUp()
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
        $this->collection->drop();
    }

    public function tearDown()
    {
        if ($this->hasFailed()) {
            return;
        }

        $this->collection->drop();
    }

    public function testIs2dSphere()
    {
        $indexName = $this->collection->createIndex(['pos' => '2dsphere']);
        $result = $this->collection->listIndexes();

        $result->rewind();
        $result->next();
        $index = $result->current();

        $this->assertEquals($indexName, $index->getName());
        $this->assertTrue($index->is2dSphere());

        $expectedVersion = version_compare($this->getServerVersion(), '3.2.0', '<') ? 2 : 3;
        $this->assertEquals($expectedVersion, $index['2dsphereIndexVersion']);
    }

    public function testIsGeoHaystack()
    {
        $indexName = $this->collection->createIndex(['pos' => 'geoHaystack', 'x' => 1], ['bucketSize' => 5]);
        $result = $this->collection->listIndexes();

        $result->rewind();
        $result->next();
        $index = $result->current();

        $this->assertEquals($indexName, $index->getName());
        $this->assertTrue($index->isGeoHaystack());
        $this->assertEquals(5, $index['bucketSize']);
    }

    public function testIsText()
    {
        $indexName = $this->collection->createIndex(['x' => 'text']);
        $result = $this->collection->listIndexes();

        $result->rewind();
        $result->next();
        $index = $result->current();

        $this->assertEquals($indexName, $index->getName());
        $this->assertTrue($index->isText());
        $this->assertEquals('english', $index['default_language']);
        $this->assertEquals('language', $index['language_override']);

        $expectedVersion = version_compare($this->getServerVersion(), '3.2.0', '<') ? 2 : 3;
        $this->assertEquals($expectedVersion, $index['textIndexVersion']);

        $this->assertSameDocument(['x' => 1], $index['weights']);
    }
}
