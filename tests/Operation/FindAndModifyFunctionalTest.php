<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\FindAndModify;
use MongoDB\Tests\CommandObserver;
use MongoDB\Tests\Fixtures\Codec\TestDocumentCodec;
use MongoDB\Tests\Fixtures\Document\TestObject;
use stdClass;

class FindAndModifyFunctionalTest extends FunctionalTestCase
{
    /** @dataProvider provideQueryDocuments */
    public function testQueryDocuments($query, stdClass $expectedQuery): void
    {
        (new CommandObserver())->observe(
            function () use ($query): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['query' => $query, 'remove' => true],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use ($expectedQuery): void {
                $this->assertEquals($expectedQuery, $event['started']->getCommand()->query ?? null);
            },
        );
    }

    public function provideQueryDocuments(): array
    {
        $expected = (object) ['x' => 1];

        return [
            'array' => [['x' => 1], $expected],
            'object' => [(object) ['x' => 1], $expected],
            'Serializable' => [new BSONDocument(['x' => 1]), $expected],
            'Document' => [Document::fromPHP(['x' => 1]), $expected],
        ];
    }

    /**
     * @dataProvider provideReplacementDocuments
     * @dataProvider provideUpdateDocuments
     * @dataProvider provideUpdatePipelines
     * @dataProvider provideReplacementDocumentLikePipeline
     */
    public function testUpdateDocuments($update, $expectedUpdate): void
    {
        (new CommandObserver())->observe(
            function () use ($update): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [
                        'query' => ['x' => 1],
                        'update' => $update,
                    ],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event) use ($expectedUpdate): void {
                $this->assertEquals($expectedUpdate, $event['started']->getCommand()->update ?? null);
            },
        );
    }

    public function provideReplacementDocumentLikePipeline(): array
    {
        /* Note: this expected value differs from UpdateFunctionalTest because
         * FindAndModify is not affected by libmongoc's pipeline detection for
         * update commands (see: CDRIVER-4658). */
        return [
            'replacement_like_pipeline' => [
                (object) ['0' => ['$set' => ['x' => 1]]],
                (object) ['0' => (object) ['$set' => (object) ['x' => 1]]],
            ],
        ];
    }

    /** @see https://jira.mongodb.org/browse/PHPLIB-344 */
    public function testManagerReadConcernIsOmitted(): void
    {
        $manager = static::createTestManager(null, ['readConcernLevel' => 'majority']);
        $server = $manager->selectServer();

        (new CommandObserver())->observe(
            function () use ($server): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true],
                );

                $operation->execute($server);
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('readConcern', $event['started']->getCommand());
            },
        );
    }

    public function testDefaultWriteConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true, 'writeConcern' => $this->createDefaultWriteConcern()],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            },
        );
    }

    public function testHintOptionUnsupportedClientSideError(): void
    {
        $this->skipIfServerVersion('>=', '4.2.0', 'server reports error for unsupported findAndModify options');

        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['remove' => true, 'hint' => '_id_'],
        );

        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('Hint is not supported by the server executing this operation');

        $operation->execute($this->getPrimaryServer());
    }

    public function testHintOptionAndUnacknowledgedWriteConcernUnsupportedClientSideError(): void
    {
        $this->skipIfServerVersion('>=', '4.4.0', 'hint is supported');

        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['remove' => true, 'hint' => '_id_', 'writeConcern' => new WriteConcern(0)],
        );

        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('Hint is not supported by the server executing this operation');

        $operation->execute($this->getPrimaryServer());
    }

    public function testFindAndModifyReportedWriteConcernError(): void
    {
        if ($this->isStandalone()) {
            $this->markTestSkipped('Test only applies to replica sets');
        }

        $this->expectException(CommandException::class);
        $this->expectExceptionCode(100 /* UnsatisfiableWriteConcern */);
        $this->expectExceptionMessageMatches('/Write Concern error:/');

        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['remove' => true, 'writeConcern' => new WriteConcern(50)],
        );

        $operation->execute($this->getPrimaryServer());
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true, 'session' => $this->createSession()],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            },
        );
    }

    public function testBypassDocumentValidationSetWhenTrue(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true, 'bypassDocumentValidation' => true],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
                $this->assertEquals(true, $event['started']->getCommand()->bypassDocumentValidation);
            },
        );
    }

    public function testBypassDocumentValidationUnsetWhenFalse(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true, 'bypassDocumentValidation' => false],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
            },
        );
    }

    /** @dataProvider provideTypeMapOptionsAndExpectedDocument */
    public function testTypeMapOption(?array $typeMap, $expectedDocument): void
    {
        $this->createFixtures(1);

        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            [
                'remove' => true,
                'typeMap' => $typeMap,
            ],
        );
        $document = $operation->execute($this->getPrimaryServer());

        $this->assertEquals($expectedDocument, $document);
    }

    public function provideTypeMapOptionsAndExpectedDocument()
    {
        return [
            [
                null,
                (object) ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
            ],
            [
                ['root' => 'array', 'document' => 'array'],
                ['_id' => 1, 'x' => ['foo' => 'bar']],
            ],
            [
                ['root' => 'object', 'document' => 'array'],
                (object) ['_id' => 1, 'x' => ['foo' => 'bar']],
            ],
            [
                ['root' => 'array', 'document' => 'stdClass'],
                ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
            ],
            [
                ['root' => BSONDocument::class, 'document' => 'object'],
                new BSONDocument(['_id' => 1, 'x' => (object) ['foo' => 'bar']]),
            ],
            [
                ['root' => 'array', 'document' => 'stdClass', 'fieldPaths' => ['x' => 'array']],
                ['_id' => 1, 'x' => ['foo' => 'bar']],
            ],
        ];
    }

    public function testFindOneAndDeleteWithCodec(): void
    {
        $this->createFixtures(1);

        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['remove' => true, 'codec' => new TestDocumentCodec()],
        );

        $result = $operation->execute($this->getPrimaryServer());

        self::assertEquals(TestObject::createDecodedForFixture(1), $result);
    }

    public function testFindOneAndDeleteNothingWithCodec(): void
    {
        // When the query does not match any documents, the operation returns null
        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['remove' => true, 'codec' => new TestDocumentCodec()],
        );

        $result = $operation->execute($this->getPrimaryServer());

        self::assertNull($result);
    }

    public function testFindOneAndUpdateWithCodec(): void
    {
        $this->createFixtures(1);

        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['update' => ['$set' => ['x.foo' => 'baz']], 'codec' => new TestDocumentCodec()],
        );

        $result = $operation->execute($this->getPrimaryServer());

        self::assertEquals(TestObject::createDecodedForFixture(1), $result);
    }

    public function testFindOneAndUpdateNothingWithCodec(): void
    {
        // When the query does not match any documents, the operation returns null
        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['update' => ['$set' => ['x.foo' => 'baz']], 'codec' => new TestDocumentCodec()],
        );

        $result = $operation->execute($this->getPrimaryServer());

        self::assertNull($result);
    }

    public function testFindOneAndReplaceWithCodec(): void
    {
        $this->createFixtures(1);

        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['update' => ['_id' => 1], 'codec' => new TestDocumentCodec()],
        );

        $result = $operation->execute($this->getPrimaryServer());

        self::assertEquals(TestObject::createDecodedForFixture(1), $result);
    }

    public function testFindOneAndReplaceNothingWithCodec(): void
    {
        // When the query does not match any documents, the operation returns null
        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['update' => ['_id' => 1], 'codec' => new TestDocumentCodec()],
        );

        $result = $operation->execute($this->getPrimaryServer());

        self::assertNull($result);
    }

    /**
     * Create data fixtures.
     */
    private function createFixtures(int $n): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert(TestObject::createDocument($i));
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
