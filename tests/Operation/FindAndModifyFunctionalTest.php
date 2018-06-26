<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\FindAndModify;
use MongoDB\Tests\CommandObserver;
use stdClass;

class FindAndModifyFunctionalTest extends FunctionalTestCase
{
    /**
     * @see https://jira.mongodb.org/browse/PHPLIB-344
     */
    public function testManagerReadConcernIsOmitted()
    {
        $manager = new Manager($this->getUri(), ['readConcernLevel' => 'majority']);
        $server = $manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        (new CommandObserver)->observe(
            function() use ($server) {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true]
                );

                $operation->execute($server);
            },
            function(array $event) {
                $this->assertObjectNotHasAttribute('readConcern', $event['started']->getCommand());
            }
        );
    }

    public function testDefaultWriteConcernIsOmitted()
    {
        (new CommandObserver)->observe(
            function() {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true, 'writeConcern' => $this->createDefaultWriteConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(array $event) {
                $this->assertObjectNotHasAttribute('writeConcern', $event['started']->getCommand());
            }
        );
    }

    public function testSessionOption()
    {
        if (version_compare($this->getServerVersion(), '3.6.0', '<')) {
            $this->markTestSkipped('Sessions are not supported');
        }

        (new CommandObserver)->observe(
            function() {
                $operation = new FindAndModify(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    ['remove' => true, 'session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function(array $event) {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }

    /**
     * @dataProvider provideTypeMapOptionsAndExpectedDocument
     */
    public function testTypeMapOption(array $typeMap = null, $expectedDocument)
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
                ['root' => 'MongoDB\Model\BSONDocument', 'document' => 'object'],
                new BSONDocument(['_id' => 1, 'x' => (object) ['foo' => 'bar']]),
            ],
        ];
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
                'x' => (object) ['foo' => 'bar'],
            ]);
        }

        $result = $this->manager->executeBulkWrite($this->getNamespace(), $bulkWrite);

        $this->assertEquals($n, $result->getInsertedCount());
    }
}
