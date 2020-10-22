<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\BadMethodCallException;
use MongoDB\Operation\Update;
use MongoDB\Tests\CommandObserver;
use MongoDB\UpdateResult;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use function version_compare;

class UpdateFunctionalTest extends FunctionalTestCase
{
    use SetUpTearDownTrait;

    /** @var Collection */
    private $collection;

    private function doSetUp()
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function () {
                $operation = new Update(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => 1],
                    ['$inc' => ['x' => 1]],
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    public function testBypassDocumentValidationSetWhenTrue()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('bypassDocumentValidation is not supported');
        }

        (new CommandObserver())->observe(
            function () {
                $operation = new Update(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => 1],
                    ['$inc' => ['x' => 1]],
                    ['bypassDocumentValidation' => true]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
                $this->assertEquals(true, $event['started']->getCommand()->bypassDocumentValidation);
            }
        );
    }

    public function testBypassDocumentValidationUnsetWhenFalse()
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('bypassDocumentValidation is not supported');
        }

        (new CommandObserver())->observe(
            function () {
                $operation = new Update(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['_id' => 1],
                    ['$inc' => ['x' => 1]],
                    ['bypassDocumentValidation' => false]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectNotHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
            }
        );
    }

    public function testUpdateOne()
    {
        $this->createFixtures(3);

        $filter = ['_id' => ['$gt' => 1]];
        $update = ['$inc' => ['x' => 1]];

        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(UpdateResult::class, $result);
        $this->assertSame(1, $result->getMatchedCount());
        $this->assertSame(1, $result->getModifiedCount());
        $this->assertSame(0, $result->getUpsertedCount());
        $this->assertNull($result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 23],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUpdateMany()
    {
        $this->createFixtures(3);

        $filter = ['_id' => ['$gt' => 1]];
        $update = ['$inc' => ['x' => 1]];
        $options = ['multi' => true];

        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(UpdateResult::class, $result);
        $this->assertSame(2, $result->getMatchedCount());
        $this->assertSame(2, $result->getModifiedCount());
        $this->assertSame(0, $result->getUpsertedCount());
        $this->assertNull($result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 23],
            ['_id' => 3, 'x' => 34],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUpdateManyWithExistingId()
    {
        $this->createFixtures(3);

        $filter = ['_id' => 5];
        $update = ['$set' => ['x' => 55]];
        $options = ['upsert' => true];

        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(UpdateResult::class, $result);
        $this->assertSame(0, $result->getMatchedCount());
        $this->assertSame(0, $result->getModifiedCount());
        $this->assertSame(1, $result->getUpsertedCount());
        $this->assertSame(5, $result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => 5, 'x' => 55],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUpdateManyWithGeneratedId()
    {
        $this->createFixtures(3);

        $filter = ['x' => 66];
        $update = ['$set' => ['x' => 66]];
        $options = ['upsert' => true];

        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(UpdateResult::class, $result);
        $this->assertSame(0, $result->getMatchedCount());
        $this->assertSame(0, $result->getModifiedCount());
        $this->assertSame(1, $result->getUpsertedCount());
        $this->assertInstanceOf(ObjectId::class, $result->getUpsertedId());

        $expected = [
            ['_id' => 1, 'x' => 11],
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
            ['_id' => $result->getUpsertedId(), 'x' => 66],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testUnacknowledgedWriteConcern()
    {
        $filter = ['_id' => 1];
        $update = ['$set' => ['x' => 1]];
        $options = ['writeConcern' => new WriteConcern(0)];
        $operation = new Update($this->getDatabaseName(), $this->getCollectionName(), $filter, $update, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($result->isAcknowledged());

        return $result;
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesMatchedCount(UpdateResult $result)
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getMatchedCount();
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesModifiedCount(UpdateResult $result)
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getModifiedCount();
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesUpsertedCount(UpdateResult $result)
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getUpsertedCount();
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesUpsertedId(UpdateResult $result)
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getUpsertedId();
    }

    /**
     * Create data fixtures.
     *
     * @param integer $n
     */
    private function createFixtures($n)
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert([
                '_id' => $i,
                'x' => (integer) ($i . $i),
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
