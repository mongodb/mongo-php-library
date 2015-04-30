<?php

namespace MongoDB\Tests\Collection\CrudSpec;

use MongoDB\Collection;
use MongoDB\FeatureDetection;
use MongoDB\Driver\ReadPreference;

/**
 * CRUD spec functional tests for aggregate().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class AggregateFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);
    }

    public function testAggregateWithMultipleStages()
    {
        $cursor = $this->collection->aggregate(
            array(
                array('$sort' => array('x' => 1)),
                array('$match' => array('_id' => array('$gt' => 1))),
            ),
            array('batchSize' => 2)
        );

        $expected = array(
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        // Use iterator_to_array() here since aggregate() may return an ArrayIterator
        $this->assertSame($expected, iterator_to_array($cursor));
    }

    public function testAggregateWithOut()
    {
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        if ( ! FeatureDetection::isSupported($server, FeatureDetection::API_AGGREGATE_CURSOR)) {
            $this->markTestSkipped('$out aggregation pipeline operator is not supported');
        }

        $outputCollection = new Collection($this->manager, $this->getNamespace() . '_output');
        $this->dropCollectionIfItExists($outputCollection);

        $this->collection->aggregate(
            array(
                array('$sort' => array('x' => 1)),
                array('$match' => array('_id' => array('$gt' => 1))),
                array('$out' => $outputCollection->getCollectionName()),
            )
        );

        $expected = array(
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $outputCollection->find()->toArray());

        // Manually clean up our output collection
        $this->dropCollectionIfItExists($outputCollection);
    }
}
