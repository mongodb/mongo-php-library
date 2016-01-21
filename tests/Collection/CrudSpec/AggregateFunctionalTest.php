<?php

namespace MongoDB\Tests\Collection\CrudSpec;

use MongoDB\Collection;
use MongoDB\Driver\ReadPreference;
use MongoDB\Operation\DropCollection;

/**
 * CRUD spec functional tests for aggregate().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class AggregateFunctionalTest extends FunctionalTestCase
{
    private static $wireVersionForOutOperator = 2;

    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);
    }

    public function testAggregateWithMultipleStages()
    {
        $cursor = $this->collection->aggregate(
            [
                ['$sort' => ['x' => 1]],
                ['$match' => ['_id' => ['$gt' => 1]]],
            ],
            ['batchSize' => 2]
        );

        $expected = [
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $cursor);
    }

    public function testAggregateWithOut()
    {
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        if ( ! \MongoDB\server_supports_feature($server, self::$wireVersionForOutOperator)) {
            $this->markTestSkipped('$out aggregation pipeline operator is not supported');
        }

        $outputCollection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName() . '_output');
        $operation = new DropCollection($this->getDatabaseName(), $outputCollection->getCollectionName());
        $operation->execute($this->getPrimaryServer());

        $this->collection->aggregate(
            [
                ['$sort' => ['x' => 1]],
                ['$match' => ['_id' => ['$gt' => 1]]],
                ['$out' => $outputCollection->getCollectionName()],
            ]
        );

        $expected = [
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $outputCollection->find());

        // Manually clean up our output collection
        $operation = new DropCollection($this->getDatabaseName(), $outputCollection->getCollectionName());
        $operation->execute($this->getPrimaryServer());
    }
}
