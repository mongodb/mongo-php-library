<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Collection;
use MongoDB\DeleteResult;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\BadMethodCallException;
use MongoDB\Operation\Delete;
use MongoDB\Tests\CommandObserver;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use function version_compare;

class DeleteFunctionalTest extends FunctionalTestCase
{
    use SetUpTearDownTrait;

    /** @var Collection */
    private $collection;

    private function doSetUp()
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
    }

    public function testDeleteOne()
    {
        $this->createFixtures(3);

        $filter = ['_id' => 1];

        $operation = new Delete($this->getDatabaseName(), $this->getCollectionName(), $filter, 1);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(DeleteResult::class, $result);
        $this->assertSame(1, $result->getDeletedCount());

        $expected = [
            ['_id' => 2, 'x' => 22],
            ['_id' => 3, 'x' => 33],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testDeleteMany()
    {
        $this->createFixtures(3);

        $filter = ['_id' => ['$gt' => 1]];

        $operation = new Delete($this->getDatabaseName(), $this->getCollectionName(), $filter, 0);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(DeleteResult::class, $result);
        $this->assertSame(2, $result->getDeletedCount());

        $expected = [
            ['_id' => 1, 'x' => 11],
        ];

        $this->assertSameDocuments($expected, $this->collection->find());
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function () {
                $operation = new Delete(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [],
                    0,
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    public function testUnacknowledgedWriteConcern()
    {
        $filter = ['_id' => 1];
        $options = ['writeConcern' => new WriteConcern(0)];

        $operation = new Delete($this->getDatabaseName(), $this->getCollectionName(), $filter, 0, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($result->isAcknowledged());

        return $result;
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesDeletedCount(DeleteResult $result)
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageRegExp('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getDeletedCount();
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
