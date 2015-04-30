<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for updateMany().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class UpdateManyFunctionalTest extends FunctionalTestCase
{
    private $omitModifiedCount;

    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);

        $this->omitModifiedCount = version_compare($this->getServerVersion(), '2.6.0', '<');
    }

    public function testUpdateManyWhenManyDocumentsMatch()
    {
        $filter = array('_id' => array('$gt' => 1));
        $update = array('$inc' => array('x' => 1));

        $result = $this->collection->updateMany($filter, $update);
        $this->assertSame(2, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(2, $result->getModifiedCount());

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 23),
            array('_id' => 3, 'x' => 34),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testUpdateManyWhenOneDocumentMatches()
    {
        $filter = array('_id' => 1);
        $update = array('$inc' => array('x' => 1));

        $result = $this->collection->updateMany($filter, $update);
        $this->assertSame(1, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(1, $result->getModifiedCount());

        $expected = array(
            array('_id' => 1, 'x' => 12),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testUpdateManyWhenNoDocumentsMatch()
    {
        $filter = array('_id' => 4);
        $update = array('$inc' => array('x' => 1));

        $result = $this->collection->updateMany($filter, $update);
        $this->assertSame(0, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(0, $result->getModifiedCount());

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testUpdateManyWithUpsertWhenNoDocumentsMatch()
    {
        $filter = array('_id' => 4);
        $update = array('$inc' => array('x' => 1));
        $options = array('upsert' => true);

        $result = $this->collection->updateMany($filter, $update, $options);
        $this->assertSame(0, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(0, $result->getModifiedCount());
        $this->assertSame(4, $result->getUpsertedId());

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
            array('_id' => 4, 'x' => 1),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }
}
