<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\FindAndModify;
use MongoDB\Tests\CommandObserver;

use function version_compare;

class FindAndModifyFunctionalTest extends FunctionalTestCase
{
    /**
     * @see https://jira.mongodb.org/browse/PHPLIB-344
     */
    public function testManagerReadConcernIsOmitted(): void
    {
        $manager = static::createTestManager(null, ['readConcernLevel' => 'majority']);
        $server = $manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        (new CommandObserver())->observe(
            function () use ($server): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true]
                );

                $operation->execute($server);
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('readConcern', $event['started']->getCommand());
            }
        );
    }

    public function testDefaultWriteConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true, 'writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );
    }

    public function testHintOptionUnsupportedClientSideError(): void
    {
        if (version_compare($this->getServerVersion(), '4.2.0', '>=')) {
            $this->markTestSkipped('server reports error for unsupported findAndModify options');
        }

        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['remove' => true, 'hint' => '_id_']
        );

        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('Hint is not supported by the server executing this operation');

        $operation->execute($this->getPrimaryServer());
    }

    public function testHintOptionAndUnacknowledgedWriteConcernUnsupportedClientSideError(): void
    {
        if (version_compare($this->getServerVersion(), '4.4.0', '>=')) {
            $this->markTestSkipped('hint is supported');
        }

        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['remove' => true, 'hint' => '_id_', 'writeConcern' => new WriteConcern(0)]
        );

        $this->expectException(UnsupportedException::class);
        $this->expectExceptionMessage('Hint is not supported by the server executing this operation');

        $operation->execute($this->getPrimaryServer());
    }

    public function testSessionOption(): void
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver())->observe(
            function (): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true, 'session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    public function testBypassDocumentValidationSetWhenTrue(): void
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('bypassDocumentValidation is not supported');
        }

        (new CommandObserver())->observe(
            function (): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true, 'bypassDocumentValidation' => true]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
                $this->assertEquals(true, $event['started']->getCommand()->bypassDocumentValidation);
            }
        );
    }

    public function testBypassDocumentValidationUnsetWhenFalse(): void
    {
        if (version_compare($this->getServerVersion(), '3.2.0', '<')) {
            $this->markTestSkipped('bypassDocumentValidation is not supported');
        }

        (new CommandObserver())->observe(
            function (): void {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true, 'bypassDocumentValidation' => false]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('bypassDocumentValidation', $event['started']->getCommand());
            }
        );
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedDocument
     */
    public function testTypeMapOption(?array $typeMap = null, $expectedDocument): void
    {
        $this->createFixtures(1);

        $operation = new FindAndModify(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            [
                'remove' => true,
                'typeMap' => $typeMap,
            ]
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

    /**
     * Create data fixtures.
     *
     * @param integer $n
     */
    private function createFixtures(int $n): void
    {
        $bulkWrite = new BulkWrite(['ordered' => true]);

        for ($i = 1; $i <= $n; $i++) {
            $bulkWrite->insert([
                '_id' => $i,
                'x' => (object) ['foo' => 'bar'],
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
