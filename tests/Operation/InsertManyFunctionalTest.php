<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\BadMethodCallException;
use MongoDB\InsertManyResult;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\InsertMany;
use MongoDB\Tests\CommandObserver;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use function version_compare;

class InsertManyFunctionalTest extends FunctionalTestCase
{
    use SetUpTearDownTrait;

    /** @var Collection */
    private $collection;

    private function doSetUp()
    {
        parent::setUp();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
    }

    public function testInsertMany()
    {
        $documents = [
            ['_id' => 'foo', 'x' => 11],
            ['x' => 22],
            (object) ['_id' => 'bar', 'x' => 33],
            new BSONDocument(['_id' => 'baz', 'x' => 44]),
        ];

        $operation = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), $documents);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertInstanceOf(InsertManyResult::class, $result);
        $this->assertSame(4, $result->getInsertedCount());

        $insertedIds = $result->getInsertedIds();
        $this->assertSame('foo', $insertedIds[0]);
        $this->assertInstanceOf(ObjectId::class, $insertedIds[1]);
        $this->assertSame('bar', $insertedIds[2]);
        $this->assertSame('baz', $insertedIds[3]);

        $expected = [
            ['_id' => 'foo', 'x' => 11],
            ['_id' => $insertedIds[1], 'x' => 22],
            ['_id' => 'bar', 'x' => 33],
            ['_id' => 'baz', 'x' => 44],
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
                $operation = new InsertMany(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['_id' => 1], ['_id' => 2]],
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
                $operation = new InsertMany(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['_id' => 1], ['_id' => 2]],
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
                $operation = new InsertMany(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [['_id' => 1], ['_id' => 2]],
                    ['bypassDocumentValidation' => false]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) {
                $this->assertObjectNotHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
            }
        );
    }

    public function testUnacknowledgedWriteConcern()
    {
        $documents = [['x' => 11]];
        $options = ['writeConcern' => new WriteConcern(0)];

        $operation = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), $documents, $options);
        $result = $operation->execute($this->getPrimaryServer());

        $this->assertFalse($result->isAcknowledged());

        return $result;
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesInsertedCount(InsertManyResult $result)
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/[\w:\\\\]+ should not be called for an unacknowledged write result/');
        $result->getInsertedCount();
    }

    /**
     * @depends testUnacknowledgedWriteConcern
     */
    public function testUnacknowledgedWriteConcernAccessesInsertedId(InsertManyResult $result)
    {
        $this->assertInstanceOf(ObjectId::class, $result->getInsertedIds()[0]);
    }
}
