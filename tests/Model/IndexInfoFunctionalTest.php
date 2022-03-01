<?php

namespace MongoDB\Tests\Model;

use MongoDB\Collection;
use MongoDB\Tests\FunctionalTestCase;

class IndexInfoFunctionalTest extends FunctionalTestCase
{
    /** @var Collection */
    private $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
        $this->collection->drop();
    }

    public function tearDown(): void
    {
        if ($this->hasFailed()) {
            return;
        }

        $this->collection->drop();

        parent::tearDown();
    }

    public function testIs2dSphere(): void
    {
        $indexName = $this->collection->createIndex(['pos' => '2dsphere']);
        $result = $this->collection->listIndexes();

        $result->rewind();
        $result->next();
        $index = $result->current();

        $this->assertEquals($indexName, $index->getName());
        $this->assertTrue($index->is2dSphere());

        // MongoDB 3.2+ reports index version 3
        $this->assertEquals(3, $index['2dsphereIndexVersion']);
    }

    /**
     * @group matrix-testing-exclude-server-5.0-driver-4.0
     * @group matrix-testing-exclude-server-5.0-driver-4.2
     * @group matrix-testing-exclude-server-5.0-driver-4.4
     */
    public function testIsGeoHaystack(): void
    {
        $this->skipIfGeoHaystackIndexIsNotSupported();

        $indexName = $this->collection->createIndex(['pos' => 'geoHaystack', 'x' => 1], ['bucketSize' => 5]);
        $result = $this->collection->listIndexes();

        $result->rewind();
        $result->next();
        $index = $result->current();

        $this->assertEquals($indexName, $index->getName());
        $this->assertTrue($index->isGeoHaystack());
        $this->assertEquals(5, $index['bucketSize']);
    }

    public function testIsText(): void
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

        // MongoDB 3.2+ reports index version 3
        $this->assertEquals(3, $index['textIndexVersion']);

        $this->assertSameDocument(['x' => 1], $index['weights']);
    }
}
