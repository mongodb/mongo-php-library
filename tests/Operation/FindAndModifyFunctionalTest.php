<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Driver\BulkWrite;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\Explain;
use MongoDB\Operation\FindAndModify;
use MongoDB\Tests\CommandObserver;
use stdClass;

class FindAndModifyFunctionalTest extends FunctionalTestCase
{
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
            function(stdClass $command) {
                $this->assertObjectNotHasAttribute('writeConcern', $command);
            }
        );
    }

    public function testExplainAllPlansExecution()
    {
        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true, 'session' => $this->createSession()]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_ALL_PLANS, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertTrue(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testExplainDefaultVerbosity()
    {
        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true, 'session' => $this->createSession()]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertTrue(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testExplainExecutionStats()
    {
        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true, 'session' => $this->createSession()]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_EXEC_STATS, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertTrue(array_key_exists('executionStats', $result));
        $this->assertFalse(array_key_exists('allPlansExecution', $result['executionStats']));
    }

    public function testExplainQueryPlanner()
    {
        $operation = new FindAndModify($this->getDatabaseName(), $this->getCollectionName(), ['remove' => true, 'session' => $this->createSession()]);

        $explainOperation = new Explain($this->getDatabaseName(), $operation, ['verbosity' => Explain::VERBOSITY_QUERY, 'typeMap' => ['root' => 'array']]);
        $result = $explainOperation->execute($this->getPrimaryServer());

        $this->assertTrue(array_key_exists('queryPlanner', $result));
        $this->assertFalse(array_key_exists('executionStats', $result));
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
            function(stdClass $command) {
                $this->assertObjectHasAttribute('lsid', $command);
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
