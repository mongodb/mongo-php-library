<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\BulkWriteResult;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\DeleteResult;
use MongoDB\InsertManyResult;
use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteResult;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Tests\FunctionalTestCase as BaseFunctionalTestCase;
use PHPUnit\Framework\SkippedTest;
use ArrayIterator;
use IteratorIterator;
use LogicException;
use MultipleIterator;
use UnexpectedValueException;

/**
 * Base class for spec test runners.
 *
 * @see https://github.com/mongodb/specifications
 */
abstract class FunctionalTestCase extends BaseFunctionalTestCase
{
    const TOPOLOGY_SINGLE = 'single';
    const TOPOLOGY_REPLICASET = 'replicaset';
    const TOPOLOGY_SHARDED = 'sharded';

    private $client;
    private $collection;
    private $configuredFailPoints = [];
    private $outcomeCollection;

    public function setUp()
    {
        parent::setUp();
        $this->configuredFailPoints = [];
    }

    public function tearDown()
    {
        $this->disableFailPoints();

        parent::tearDown();
    }

    /**
     * Assert an operation's outcome.
     *
     * @param array $operation
     * @param array $outcome
     */
    protected function assertOperation(array $operation, array $outcome)
    {
        $result = null;
        $exception = null;

        $expectedError = array_key_exists('error', $outcome) ? $outcome['error'] : false;

        try {
            $result = $this->executeOperation($operation);
            $this->assertFalse($expectedError);
        } catch (RuntimeException $e) {
            $exception = $e;
            $this->assertTrue($expectedError);
        }

        // Extract incomplete result for failed bulkWrite and insertMany ops
        if ($exception instanceof BulkWriteException) {
            $result = $exception->getWriteResult();
        }

        // TODO: Remove this once ext-mongodb is bumped to 1.6.0 (see: PHPC-1373)
        if ($expectedError && !($exception instanceof BulkWriteException) &&
            in_array($operation['name'], ['bulkWrite', 'insertMany']) &&
            array_key_exists('result', $outcome)) {
            $this->markTestSkipped('WriteResult is inaccessible for bulk write error');
        }

        if (array_key_exists('result', $outcome)) {
            $this->assertOperationResult($operation, $outcome['result'], $result);
        }
    }

    /**
     * Asserts the result of an operation.
     *
     * @param array $operation
     * @param mixed $expectedResult
     * @param mixed $actualResult
     * @throws LogicException if the operation is unsupported
     */
    protected function assertOperationResult(array $operation, $expectedResult, $actualResult)
    {
        switch ($operation['name']) {
            case 'aggregate':
                /* Returning a cursor for the $out collection is optional per
                 * the CRUD specification and is not implemented in the library
                 * since we have no concept of lazy cursors. We will not assert
                 * the result here; however, assertOutcomeCollectionData() will
                 * assert the output collection's contents later.
                 */
                if ( ! \MongoDB\is_last_pipeline_operator_out($operation['arguments']['pipeline'])) {
                    $this->assertSameDocuments($expectedResult, $actualResult);
                }
                break;

            case 'bulkWrite':
                $this->assertInternalType('array', $expectedResult);
                $this->assertThat($actualResult, $this->logicalOr(
                    $this->isInstanceOf(BulkWriteResult::class),
                    $this->isInstanceOf(WriteResult::class)
                ));

                if (isset($expectedResult['deletedCount'])) {
                    $this->assertSame($expectedResult['deletedCount'], $actualResult->getDeletedCount());
                }

                if (isset($expectedResult['insertedCount'])) {
                    $this->assertSame($expectedResult['insertedCount'], $actualResult->getInsertedCount());
                }

                // insertedIds are not available after BulkWriteException (see: PHPLIB-428)
                if (isset($expectedResult['insertedIds']) && $actualResult instanceof BulkWriteResult) {
                    $this->assertSameDocument(
                        ['insertedIds' => $expectedResult['insertedIds']],
                        ['insertedIds' => $actualResult->getInsertedIds()]
                    );
                }

                if (isset($expectedResult['matchedCount'])) {
                    $this->assertSame($expectedResult['matchedCount'], $actualResult->getMatchedCount());
                }

                if (isset($expectedResult['modifiedCount'])) {
                    $this->assertSame($expectedResult['modifiedCount'], $actualResult->getModifiedCount());
                }

                if (isset($expectedResult['upsertedCount'])) {
                    $this->assertSame($expectedResult['upsertedCount'], $actualResult->getUpsertedCount());
                }

                if (isset($expectedResult['upsertedIds'])) {
                    $this->assertSameDocument(
                        ['upsertedIds' => $expectedResult['upsertedIds']],
                        ['upsertedIds' => $actualResult->getUpsertedIds()]
                    );
                }
                break;

            case 'count':
            case 'countDocuments':
            case 'estimatedDocumentCount':
                $this->assertSame($expectedResult, $actualResult);
                break;

            case 'distinct':
                $this->assertSameDocument(
                    ['values' => $expectedResult],
                    ['values' => $actualResult]
                );
                break;

            case 'find':
                $this->assertSameDocuments($expectedResult, $actualResult);
                break;

            case 'deleteMany':
            case 'deleteOne':
                $this->assertInternalType('array', $expectedResult);
                $this->assertInstanceOf(DeleteResult::class, $actualResult);

                if (isset($expectedResult['deletedCount'])) {
                    $this->assertSame($expectedResult['deletedCount'], $actualResult->getDeletedCount());
                }
                break;

            case 'findOneAndDelete':
            case 'findOneAndReplace':
            case 'findOneAndUpdate':
                $this->assertSameDocument(
                    ['result' => $expectedResult],
                    ['result' => $actualResult]
                );
                break;

            case 'insertMany':
                $this->assertInternalType('array', $expectedResult);
                $this->assertThat($actualResult, $this->logicalOr(
                    $this->isInstanceOf(InsertManyResult::class),
                    $this->isInstanceOf(WriteResult::class)
                ));

                if (isset($expectedResult['insertedCount'])) {
                    $this->assertSame($expectedResult['insertedCount'], $actualResult->getInsertedCount());
                }

                // insertedIds are not available after BulkWriteException (see: PHPLIB-428)
                if (isset($expectedResult['insertedIds']) && $actualResult instanceof BulkWriteResult) {
                    $this->assertSameDocument(
                        ['insertedIds' => $expectedResult['insertedIds']],
                        ['insertedIds' => $actualResult->getInsertedIds()]
                    );
                }
                break;

            case 'insertOne':
                $this->assertInternalType('array', $expectedResult);
                $this->assertInstanceOf(InsertOneResult::class, $actualResult);

                if (isset($expectedResult['insertedCount'])) {
                    $this->assertSame($expectedResult['insertedCount'], $actualResult->getInsertedCount());
                }

                if (isset($expectedResult['insertedId'])) {
                    $this->assertSameDocument(
                        ['insertedId' => $expectedResult['insertedId']],
                        ['insertedId' => $actualResult->getInsertedId()]
                    );
                }
                break;

            case 'replaceOne':
            case 'updateMany':
            case 'updateOne':
                $this->assertInternalType('array', $expectedResult);
                $this->assertInstanceOf(UpdateResult::class, $actualResult);

                if (isset($expectedResult['matchedCount'])) {
                    $this->assertSame($expectedResult['matchedCount'], $actualResult->getMatchedCount());
                }

                if (isset($expectedResult['modifiedCount'])) {
                    $this->assertSame($expectedResult['modifiedCount'], $actualResult->getModifiedCount());
                }

                if (isset($expectedResult['upsertedCount'])) {
                    $this->assertSame($expectedResult['upsertedCount'], $actualResult->getUpsertedCount());
                }

                if (array_key_exists('upsertedId', $expectedResult)) {
                    $this->assertSameDocument(
                        ['upsertedId' => $expectedResult['upsertedId']],
                        ['upsertedId' => $actualResult->getUpsertedId()]
                    );
                }
                break;

            default:
                throw new LogicException('Unsupported operation: ' . $operation['name']);
        }
    }

    /**
     * Assert data within the outcome collection.
     *
     * @param array $expectedDocuments
     */
    protected function assertOutcomeCollectionData(array $expectedDocuments)
    {
        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($expectedDocuments));
        $mi->attachIterator(new IteratorIterator($this->outcomeCollection->find()));

        foreach ($mi as $documents) {
            list($expectedDocument, $actualDocument) = $documents;
            $this->assertSameDocument($expectedDocument, $actualDocument);
        }
    }

    /**
     * Checks server version and topology requirements.
     *
     * @param array $runOn
     * @throws SkippedTest if the server requirements are not met
     */
    protected function checkServerRequirements(array $runOn)
    {
        foreach ($runOn as $req) {
            $minServerVersion = isset($req['minServerVersion']) ? $req['minServerVersion'] : null;
            $maxServerVersion = isset($req['maxServerVersion']) ? $req['maxServerVersion'] : null;
            $topologies = isset($req['topology']) ? $req['topology'] : null;

            if ($this->isServerRequirementSatisifed($minServerVersion, $maxServerVersion, $topologies)) {
                return;
            }
        }

        $serverVersion = $this->getServerVersion();
        $topology = $this->getTopology();

        $this->markTestSkipped(sprintf('Server version "%s" and topology "%s" do not meet test requirements: %s', $serverVersion, $topology, json_encode($runOn)));
    }

    /**
     * Configure a fail point for the test.
     *
     * The fail point will automatically be disabled during tearDown() to avoid
     * affecting a subsequent test.
     *
     * @param array $failPointCommand
     */
    protected function configureFailPoint(array $failPointCommand)
    {
        $database = new Database($this->manager, 'admin');
        $cursor = $database->command($failPointCommand);
        $result = $cursor->toArray()[0];

        $this->assertCommandSucceeded($result);

        // Record the fail point so it can be disabled during tearDown()
        $this->configuredFailPoints[] = $failPointCommand['configureFailPoint'];
    }

    /**
     * Initialize data fixtures in test subject.
     *
     * @param array $documents
     */
    protected function initDataFixtures(array $documents)
    {
        if (empty($documents)) {
            return;
        }

        $this->collection->insertMany($documents);
    }

    /**
     * Initialize outcome collection.
     *
     * @param array $test
     */
    protected function initOutcomeCollection(array $test)
    {
        $outcomeCollectionName = isset($test['outcome']['collection']['name'])
            ? $test['outcome']['collection']['name']
            : $this->collection->getCollectionName();

        // Outcome collection need not use client under test
        $this->outcomeCollection = new Collection($this->manager, $this->getDatabaseName(), $outcomeCollectionName);

        // Avoid a redundant drop if the test subject and outcome are the same
        if ($this->collection->getNamespace() !== $this->outcomeCollection->getNamespace()) {
            $this->outcomeCollection->drop();
        }
    }

    /**
     * Initialize client and collection objects.
     *
     * @param array $test
     * @throws LogicException if an option is unsupported
     */
    protected function initTestSubjects(array $test)
    {
        // TODO: Revise this once a test environment with multiple mongos nodes is available (see: PHPLIB-430)
        if (isset($test['useMultipleMongoses']) && $test['useMultipleMongoses'] && $this->isShardedCluster()) {
            $this->markTestSkipped('"useMultipleMongoses" is not supported');
        }

        $clientOptions = isset($test['clientOptions']) ? $test['clientOptions'] : [];

        $this->client = new Client($this->getUri(), $clientOptions);
        $this->collection = $this->client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $this->collection->drop();
    }

    /**
     * Disables any fail points that were configured earlier in the test.
     *
     * This tracks fail points set via configureFailPoint() and should be called
     * during tearDown().
     */
    private function disableFailPoints()
    {
        $database = new Database($this->manager, 'admin');

        foreach ($this->configuredFailPoints as $failPoint) {
            $database->command(['configureFailPoint' => $failPoint, 'mode' => 'off']);
        }
    }

    /**
     * Executes an "operation" block.
     *
     * @param array $operation
     * @return mixed
     * @throws LogicException if the operation is unsupported
     */
    private function executeOperation(array $operation)
    {
        switch ($operation['name']) {
            case 'aggregate':
                return $this->collection->aggregate(
                    $operation['arguments']['pipeline'],
                    array_diff_key($operation['arguments'], ['pipeline' => 1])
                );

            case 'bulkWrite':
                return $this->collection->bulkWrite(
                    array_map([$this, 'prepareBulkWriteRequest'], $operation['arguments']['requests']),
                    isset($operation['arguments']['options']) ? $operation['arguments']['options'] : []
                );

            case 'count':
            case 'countDocuments':
            case 'find':
                return $this->collection->{$operation['name']}(
                    isset($operation['arguments']['filter']) ? $operation['arguments']['filter'] : [],
                    array_diff_key($operation['arguments'], ['filter' => 1])
                );

            case 'estimatedDocumentCount':
                return $this->collection->estimatedDocumentCount($operation['arguments']);

            case 'deleteMany':
            case 'deleteOne':
            case 'findOneAndDelete':
                return $this->collection->{$operation['name']}(
                    $operation['arguments']['filter'],
                    array_diff_key($operation['arguments'], ['filter' => 1])
                );

            case 'distinct':
                return $this->collection->distinct(
                    $operation['arguments']['fieldName'],
                    isset($operation['arguments']['filter']) ? $operation['arguments']['filter'] : [],
                    array_diff_key($operation['arguments'], ['fieldName' => 1, 'filter' => 1])
                );

            case 'findOneAndReplace':
                $operation['arguments'] = $this->prepareFindAndModifyArguments($operation['arguments']);
                // Fall through

            case 'replaceOne':
                return $this->collection->{$operation['name']}(
                    $operation['arguments']['filter'],
                    $operation['arguments']['replacement'],
                    array_diff_key($operation['arguments'], ['filter' => 1, 'replacement' => 1])
                );

            case 'findOneAndUpdate':
                $operation['arguments'] = $this->prepareFindAndModifyArguments($operation['arguments']);
                // Fall through

            case 'updateMany':
            case 'updateOne':
                return $this->collection->{$operation['name']}(
                    $operation['arguments']['filter'],
                    $operation['arguments']['update'],
                    array_diff_key($operation['arguments'], ['filter' => 1, 'update' => 1])
                );

            case 'insertMany':
                return $this->collection->insertMany(
                    $operation['arguments']['documents'],
                    isset($operation['arguments']['options']) ? $operation['arguments']['options'] : []
                );

            case 'insertOne':
                return $this->collection->insertOne(
                    $operation['arguments']['document'],
                    array_diff_key($operation['arguments'], ['document' => 1])
                );

            default:
                throw new LogicException('Unsupported operation: ' . $operation['name']);
        }
    }

    /**
     * Return the corresponding topology constants for the current topology.
     *
     * @return string
     * @throws UnexpectedValueException if topology is neither single nor RS nor sharded
     */
    private function getTopology()
    {
        $topologyTypeMap = [
            Server::TYPE_STANDALONE => self::TOPOLOGY_SINGLE,
            Server::TYPE_RS_PRIMARY => self::TOPOLOGY_REPLICASET,
            Server::TYPE_MONGOS => self::TOPOLOGY_SHARDED,
        ];

        $primaryType = $this->getPrimaryServer()->getType();

        if (isset($topologyTypeMap[$primaryType])) {
            return $topologyTypeMap[$primaryType];
        }

        throw new UnexpectedValueException('Toplogy is neither single nor RS nor sharded');
    }

    /**
     * Checks if server version and topology requirements are satifised.
     *
     * @param string|null $minServerVersion
     * @param string|null $maxServerVersion
     * @param array|null  $topologies
     * @return boolean
     */
    private function isServerRequirementSatisifed($minServerVersion, $maxServerVersion, array $topologies = null)
    {
        $serverVersion = $this->getServerVersion();

        if (isset($minServerVersion) && version_compare($serverVersion, $minServerVersion, '<')) {
            return false;
        }

        if (isset($maxServerVersion) && version_compare($serverVersion, $maxServerVersion, '>')) {
            return false;
        }

        $topology = $this->getTopology();

        if (isset($topologies) && ! in_array($topology, $topologies)) {
            return false;
        }

        return true;
    }

    /**
     * Prepares a request element for a bulkWrite operation.
     *
     * @param array $request
     * @return array
     */
    private function prepareBulkWriteRequest(array $request)
    {
        switch ($request['name']) {
            case 'deleteMany':
            case 'deleteOne':
                return [ $request['name'] => [
                    $request['arguments']['filter'],
                    array_diff_key($request['arguments'], ['filter' => 1]),
                ]];

            case 'insertOne':
                return [ 'insertOne' => [ $request['arguments']['document'] ]];

            case 'replaceOne':
                return [ 'replaceOne' => [
                    $request['arguments']['filter'],
                    $request['arguments']['replacement'],
                    array_diff_key($request['arguments'], ['filter' => 1, 'replacement' => 1]),
                ]];

            case 'updateMany':
            case 'updateOne':
                return [ $request['name'] => [
                    $request['arguments']['filter'],
                    $request['arguments']['update'],
                    array_diff_key($request['arguments'], ['filter' => 1, 'update' => 1]),
                ]];

            default:
                throw new LogicException('Unsupported bulk write request: ' . $request['name']);
        }
    }

    /**
     * Prepares arguments for findOneAndReplace and findOneAndUpdate operations.
     *
     * @param array $arguments
     * @return array
     */
    private function prepareFindAndModifyArguments(array $arguments)
    {
        if (isset($arguments['returnDocument'])) {
            $arguments['returnDocument'] = ('after' === strtolower($arguments['returnDocument']))
                ? FindOneAndReplace::RETURN_DOCUMENT_AFTER
                : FindOneAndReplace::RETURN_DOCUMENT_BEFORE;
        }

        return $arguments;
    }
}
