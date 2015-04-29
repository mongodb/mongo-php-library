<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for updateOne().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class UpdateOneFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);
    }

    public function testUpdateOneWhenManyDocumentsMatch()
    {
        $filter = array('_id' => array('$gt' => 1));
        $update = array('$inc' => array('x' => 1));

        $result = $this->collection->updateOne($filter, $update);
        $this->assertSame(1, $result->getMatchedCount());
        $this->assertSame(1, $result->getModifiedCount());

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 23),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testUpdateOneWhenOneDocumentMatches()
    {
        $filter = array('_id' => 1);
        $update = array('$inc' => array('x' => 1));

        $result = $this->collection->updateOne($filter, $update);
        $this->assertSame(1, $result->getMatchedCount());
        $this->assertSame(1, $result->getModifiedCount());

        $expected = array(
            array('_id' => 1, 'x' => 12),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testUpdateOneWhenNoDocumentsMatch()
    {
        $filter = array('_id' => 4);
        $update = array('$inc' => array('x' => 1));

        $result = $this->collection->updateOne($filter, $update);
        $this->assertSame(0, $result->getMatchedCount());
        $this->assertSame(0, $result->getModifiedCount());

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testUpdateOneWithUpsertWhenNoDocumentsMatch()
    {
        $filter = array('_id' => 4);
        $update = array('$inc' => array('x' => 1));
        $options = array('upsert' => true);

        $result = $this->collection->updateOne($filter, $update, $options);
        $this->assertSame(0, $result->getMatchedCount());
        $this->assertSame(0, $result->getModifiedCount());
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
