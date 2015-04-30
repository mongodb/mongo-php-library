<?php

namespace MongoDB\Tests\Collection\CrudSpec;

/**
 * CRUD spec functional tests for replaceOne().
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests
 */
class ReplaceOneFunctionalTest extends FunctionalTestCase
{
    private $omitModifiedCount;

    public function setUp()
    {
        parent::setUp();

        $this->createFixtures(3);

        $this->omitModifiedCount = version_compare($this->getServerVersion(), '2.6.0', '<');
    }

    public function testReplaceOneWhenManyDocumentsMatch()
    {
        $filter = array('_id' => array('$gt' => 1));
        $replacement = array('x' => 111);

        $result = $this->collection->replaceOne($filter, $replacement);
        $this->assertSame(1, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(1, $result->getModifiedCount());

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 111),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testReplaceOneWhenOneDocumentMatches()
    {
        $filter = array('_id' => 1);
        $replacement = array('x' => 111);

        $result = $this->collection->replaceOne($filter, $replacement);
        $this->assertSame(1, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(1, $result->getModifiedCount());

        $expected = array(
            array('_id' => 1, 'x' => 111),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testReplaceOneWhenNoDocumentsMatch()
    {
        $filter = array('_id' => 4);
        $replacement = array('x' => 111);

        $result = $this->collection->replaceOne($filter, $replacement);
        $this->assertSame(0, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(0, $result->getModifiedCount());

        $expected = array(
            array('_id' => 1, 'x' => 11),
            array('_id' => 2, 'x' => 22),
            array('_id' => 3, 'x' => 33),
        );

        $this->assertSame($expected, $this->collection->find()->toArray());
    }

    public function testReplaceOneWithUpsertWhenNoDocumentsMatchWithAnIdSpecified()
    {
        $filter = array('_id' => 4);
        $replacement = array('_id' => 4, 'x' => 1);
        $options = array('upsert' => true);

        $result = $this->collection->replaceOne($filter, $replacement, $options);
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

    public function testReplaceOneWithUpsertWhenNoDocumentsMatchWithoutAnIdSpecified()
    {
        $filter = array('_id' => 4);
        // Server 2.4 and earlier requires any custom ID to also be in the replacement document
        $replacement = array('_id' => 4, 'x' => 1);
        $options = array('upsert' => true);

        $result = $this->collection->replaceOne($filter, $replacement, $options);
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
