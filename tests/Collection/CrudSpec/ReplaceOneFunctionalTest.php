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
        $filter = ['_id' => ['$gt' => 1]];
        $replacement = ['x' => 111];

        $result = $this->collection->replaceOne($filter, $replacement);
        $this->assertSame(1, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(1, $result->getModifiedCount());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 111],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testReplaceOneWhenOneDocumentMatches()
    {
        $filter = ['_id' => 1];
        $replacement = ['x' => 111];

        $result = $this->collection->replaceOne($filter, $replacement);
        $this->assertSame(1, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(1, $result->getModifiedCount());

        $expected = [
            ['_id' => 1, 'x' => 111],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testReplaceOneWhenNoDocumentsMatch()
    {
        $filter = ['_id' => 4];
        $replacement = ['x' => 111];

        $result = $this->collection->replaceOne($filter, $replacement);
        $this->assertSame(0, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(0, $result->getModifiedCount());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testReplaceOneWithUpsertWhenNoDocumentsMatchWithAnIdSpecified()
    {
        $filter = ['_id' => 4];
        $replacement = ['_id' => 4, 'x' => 1];
        $options = ['upsert' => true];

        $result = $this->collection->replaceOne($filter, $replacement, $options);
        $this->assertSame(0, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(0, $result->getModifiedCount());
        $this->assertSame(4, $result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => 4, 'x' => 1],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testReplaceOneWithUpsertWhenNoDocumentsMatchWithoutAnIdSpecified()
    {
        $filter = ['_id' => 4];
        // Server 2.4 and earlier requires any custom ID to also be in the replacement document
        $replacement = ['_id' => 4, 'x' => 1];
        $options = ['upsert' => true];

        $result = $this->collection->replaceOne($filter, $replacement, $options);
        $this->assertSame(0, $result->getMatchedCount());
        $this->omitModifiedCount or $this->assertSame(0, $result->getModifiedCount());
        $this->assertSame(4, $result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => 4, 'x' => 1],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }
}
