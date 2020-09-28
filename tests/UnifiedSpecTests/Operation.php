<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use LogicException;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Model\IndexInfo;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Operation\FindOneAndUpdate;
use stdClass;
use function array_diff_key;
use function array_map;
use function get_class;
use function iterator_to_array;
use function MongoDB\is_last_pipeline_operator_write;
use function strtolower;

/**
 * Spec test operation.
 */
final class Operation
{
    const OBJECT_TEST_RUNNER = 'testRunner';

    /** @var string */
    private $name;

    /** @var string */
    private $object;

    /** @var array */
    private $arguments = [];

    /** @var ExpectedError */
    private $expectedError;

    /** @var ExpectedResult */
    private $expectedResult;

    /** @var bool */
    private $saveResultAsEntity;

    public function __construct(stdClass $o)
    {
        assertInternalType('string', $o->name);
        $this->name = $o->name;

        assertInternalType('string', $o->object);
        $this->object = $o->object;

        if (isset($o->arguments)) {
            assertInternalType('object', $o->arguments);
            $this->arguments = (array) $o->arguments;
        }

        // expectError is mutually exclusive with expectResult and saveResultAsEntity
        assertThat($o, logicalXor(
            objectHasAttribute('expectError'),
            logicalOr(objectHasAttribute('expectResult'), objectHasAttribute('saveResultAsEntity'))
        ));

        $this->expectError = ExpectedError::fromOperation($o);

        if (isset($o->expectResult)) {
            $this->expectResult = ExpectedResult::fromOperation($o);
        }

        if (isset($o->saveResultAsEntity)) {
            assertInternalType('string', $o->saveResultAsEntity);
            $this->saveResultAsEntity = $o->saveResultAsEntity;
        }
    }

    /**
     * Execute the operation and assert its outcome.
     */
    public function assert(Context $context, bool $rethrowExceptions = false)
    {
        $throwable = null;
        $result = null;

        try {
            $result = $this->execute($context);

            /* Eagerly iterate the results of a cursor. This both allows an
             * exception to be thrown sooner and ensures that any expected
             * getMore command(s) can be observed even if a ResultExpectation
             * is not used (e.g. Command Monitoring spec). */
            if ($result instanceof Cursor) {
                $result = $result->toArray();
            }
        } catch (Throwable $e) {
            $error = $e;
        }

        $this->expectError->assert($throwable);
        $this->expectResult->assert($result);

        // Rethrowing is primarily used for withTransaction callbacks
        if ($error && $rethrowExceptions) {
            throw $error;
        }
    }

    /**
     * Executes the operation with a given context.
     *
     * @param Context $context
     * @return mixed
     * @throws LogicException if the entity type or operation is unsupported
     */
    private function execute(Context $context)
    {
        if ($this->object == self::OBJECT_TEST_RUNNER) {
            return $this->executeForTestRunner($context);
        }

        $entityMap = $context->getEntityMap();

        assertArrayHasKey($this->object, $entityMap);
        $object = $entityMap[$this->object];
        assertInternalType('object', $object);

        switch (get_class($object)) {
            case Client::class:
                return $this->executeForClient($object, $context);
            case Database::class:
                return $this->executeForDatabase($object, $context);
            case Collection::class:
                return $this->executeForCollection($object, $context);
            default:
                Assert::fail('Unsupported entity type: ' . get_class($object));
        }
    }

    private function executeForClient(Client $client, Context $context)
    {
        $args = $context->prepareOperationArguments($this->arguments);

        switch ($this->name) {
            case 'listDatabaseNames':
                return iterator_to_array($client->listDatabaseNames($args));
            case 'listDatabases':
                return $client->listDatabases($args);
            case 'watch':
                return $client->watch(
                    $args['pipeline'] ?? [],
                    array_diff_key($args, ['pipeline' => 1])
                );
            default:
                Assert::fail('Unsupported client operation: ' . $this->name);
        }
    }

    private function executeForCollection(Collection $collection, Context $context)
    {
        $args = $context->prepareOperationArguments($this->arguments);

        switch ($this->name) {
            case 'aggregate':
                return $collection->aggregate(
                    $args['pipeline'],
                    array_diff_key($args, ['pipeline' => 1])
                );
            case 'bulkWrite':
                // Merge nested and top-level options (see: SPEC-1158)
                $options = isset($args['options']) ? (array) $args['options'] : [];
                $options += array_diff_key($args, ['requests' => 1]);

                return $collection->bulkWrite(
                    // TODO: Check if self can be used with a private static function
                    array_map([$this, 'prepareBulkWriteRequest'], $args['requests']),
                    $options
                );
            case 'createIndex':
                return $collection->createIndex(
                    $args['keys'],
                    array_diff_key($args, ['keys' => 1])
                );
            case 'dropIndex':
                return $collection->dropIndex(
                    $args['name'],
                    array_diff_key($args, ['name' => 1])
                );
            case 'count':
            case 'countDocuments':
            case 'find':
                return $collection->{$this->name}(
                    $args['filter'] ?? [],
                    array_diff_key($args, ['filter' => 1])
                );
            case 'estimatedDocumentCount':
                return $collection->estimatedDocumentCount($args);
            case 'deleteMany':
            case 'deleteOne':
            case 'findOneAndDelete':
                return $collection->{$this->name}(
                    $args['filter'],
                    array_diff_key($args, ['filter' => 1])
                );
            case 'distinct':
                return $collection->distinct(
                    $args['fieldName'],
                    $args['filter'] ?? [],
                    array_diff_key($args, ['fieldName' => 1, 'filter' => 1])
                );
            case 'drop':
                return $collection->drop($args);
            case 'findOne':
                return $collection->findOne($args['filter'], array_diff_key($args, ['filter' => 1]));
            case 'findOneAndReplace':
                if (isset($args['returnDocument'])) {
                    $args['returnDocument'] = 'after' === strtolower($args['returnDocument'])
                        ? FindOneAndReplace::RETURN_DOCUMENT_AFTER
                        : FindOneAndReplace::RETURN_DOCUMENT_BEFORE;
                }
                // Fall through

            case 'replaceOne':
                return $collection->{$this->name}(
                    $args['filter'],
                    $args['replacement'],
                    array_diff_key($args, ['filter' => 1, 'replacement' => 1])
                );
            case 'findOneAndUpdate':
                if (isset($args['returnDocument'])) {
                    $args['returnDocument'] = 'after' === strtolower($args['returnDocument'])
                        ? FindOneAndUpdate::RETURN_DOCUMENT_AFTER
                        : FindOneAndUpdate::RETURN_DOCUMENT_BEFORE;
                }
                // Fall through

            case 'updateMany':
            case 'updateOne':
                return $collection->{$this->name}(
                    $args['filter'],
                    $args['update'],
                    array_diff_key($args, ['filter' => 1, 'update' => 1])
                );
            case 'insertMany':
                // Merge nested and top-level options (see: SPEC-1158)
                $options = isset($args['options']) ? (array) $args['options'] : [];
                $options += array_diff_key($args, ['documents' => 1]);

                return $collection->insertMany(
                    $args['documents'],
                    $options
                );
            case 'insertOne':
                return $collection->insertOne(
                    $args['document'],
                    array_diff_key($args, ['document' => 1])
                );
            case 'listIndexes':
                return $collection->listIndexes($args);
            case 'mapReduce':
                return $collection->mapReduce(
                    $args['map'],
                    $args['reduce'],
                    $args['out'],
                    array_diff_key($args, ['map' => 1, 'reduce' => 1, 'out' => 1])
                );
            case 'watch':
                return $collection->watch(
                    $args['pipeline'] ?? [],
                    array_diff_key($args, ['pipeline' => 1])
                );
            default:
                Assert::fail('Unsupported collection operation: ' . $this->name);
        }
    }

    private function executeForDatabase(Database $database, Context $context)
    {
        $args = $context->prepareOperationArguments($this->arguments);

        switch ($this->name) {
            case 'aggregate':
                return $database->aggregate(
                    $args['pipeline'],
                    array_diff_key($args, ['pipeline' => 1])
                );
            case 'createCollection':
                return $database->createCollection(
                    $args['collection'],
                    array_diff_key($args, ['collection' => 1])
                );
            case 'dropCollection':
                return $database->dropCollection(
                    $args['collection'],
                    array_diff_key($args, ['collection' => 1])
                );
            case 'listCollectionNames':
                return iterator_to_array($database->listCollectionNames($args));
            case 'listCollections':
                return $database->listCollections($args);
            case 'runCommand':
                return $database->command(
                    $args['command'],
                    array_diff_key($args, ['command' => 1])
                )->toArray()[0];
            case 'watch':
                return $database->watch(
                    $args['pipeline'] ?? [],
                    array_diff_key($args, ['pipeline' => 1])
                );
            default:
                Assert::fail('Unsupported database operation: ' . $this->name);
        }
    }

    private function executeForTestRunner(Context $context)
    {
        $args = $context->prepareOperationArguments($this->arguments);

        switch ($this->name) {
            case 'assertCollectionExists':
                $databaseName = $args['database'];
                $collectionName = $args['collection'];

                $test->assertContains($collectionName, $context->selectDatabase($databaseName)->listCollectionNames());

                return null;
            case 'assertCollectionNotExists':
                $databaseName = $args['database'];
                $collectionName = $args['collection'];

                $test->assertNotContains($collectionName, $context->selectDatabase($databaseName)->listCollectionNames());

                return null;
            case 'assertIndexExists':
                $databaseName = $args['database'];
                $collectionName = $args['collection'];
                $indexName = $args['index'];

                $test->assertContains($indexName, $this->getIndexNames($context, $databaseName, $collectionName));

                return null;
            case 'assertIndexNotExists':
                $databaseName = $args['database'];
                $collectionName = $args['collection'];
                $indexName = $args['index'];

                $test->assertNotContains($indexName, $this->getIndexNames($context, $databaseName, $collectionName));

                return null;
            case 'assertSessionPinned':
                $test->assertInstanceOf(Session::class, $args['session']);
                $test->assertInstanceOf(Server::class, $args['session']->getServer());

                return null;
            case 'assertSessionTransactionState':
                $test->assertInstanceOf(Session::class, $args['session']);
                /* PHPC currently does not expose the exact session state, but
                 * instead exposes a bool to let us know whether a transaction
                 * is currently in progress. This code may fail down the line
                 * and should be adjusted once PHPC-1438 is implemented. */
                $test->assertSame($this->arguments['state'], $args['session']->getTransactionState());

                return null;
            case 'assertSessionUnpinned':
                $test->assertInstanceOf(Session::class, $args['session']);
                $test->assertNull($args['session']->getServer());

                return null;
            case 'targetedFailPoint':
                $test->assertInstanceOf(Session::class, $args['session']);
                $test->configureFailPoint($this->arguments['failPoint'], $args['session']->getServer());

                return null;
            default:
                throw new LogicException('Unsupported test runner operation: ' . $this->name);
        }
    }

    /**
     * @param string $databaseName
     * @param string $collectionName
     *
     * @return array
     */
    private function getIndexNames(Context $context, $databaseName, $collectionName)
    {
        return array_map(
            function (IndexInfo $indexInfo) {
                return $indexInfo->getName();
            },
            iterator_to_array($context->selectCollection($databaseName, $collectionName)->listIndexes())
        );
    }

    /**
     * @throws LogicException if the operation object is unsupported
     */
    private function getResultAssertionType()
    {
        switch ($this->object) {
            case self::OBJECT_CLIENT:
                return $this->getResultAssertionTypeForClient();
            case self::OBJECT_COLLECTION:
                return $this->getResultAssertionTypeForCollection();
            case self::OBJECT_DATABASE:
                return $this->getResultAssertionTypeForDatabase();
            case self::OBJECT_GRIDFS_BUCKET:
                return ResultExpectation::ASSERT_SAME;
            case self::OBJECT_SESSION0:
            case self::OBJECT_SESSION1:
            case self::OBJECT_TEST_RUNNER:
                return ResultExpectation::ASSERT_NOTHING;
            default:
                throw new LogicException('Unsupported object: ' . $this->object);
        }
    }

    /**
     * @throws LogicException if the collection operation is unsupported
     */
    private function getResultAssertionTypeForClient()
    {
        switch ($this->name) {
            case 'listDatabaseNames':
                return ResultExpectation::ASSERT_SAME;
            case 'listDatabases':
                return ResultExpectation::ASSERT_SAME_DOCUMENTS;
            case 'watch':
                return ResultExpectation::ASSERT_SAME_DOCUMENTS;
            default:
                throw new LogicException('Unsupported client operation: ' . $this->name);
        }
    }

    /**
     * @throws LogicException if the collection operation is unsupported
     */
    private function getResultAssertionTypeForCollection()
    {
        switch ($this->name) {
            case 'aggregate':
                /* Returning a cursor for the $out collection is optional per
                 * the CRUD specification and is not implemented in the library
                 * since we have no concept of lazy cursors. Rely on examining
                 * the output collection rather than the operation result. */
                if (is_last_pipeline_operator_write($this->arguments['pipeline'])) {
                    return ResultExpectation::ASSERT_NOTHING;
                }

                return ResultExpectation::ASSERT_SAME_DOCUMENTS;
            case 'bulkWrite':
                return ResultExpectation::ASSERT_BULKWRITE;
            case 'count':
            case 'countDocuments':
                return ResultExpectation::ASSERT_SAME;
            case 'createIndex':
            case 'dropIndex':
                return ResultExpectation::ASSERT_MATCHES_DOCUMENT;
            case 'distinct':
            case 'estimatedDocumentCount':
                return ResultExpectation::ASSERT_SAME;
            case 'deleteMany':
            case 'deleteOne':
                return ResultExpectation::ASSERT_DELETE;
            case 'drop':
                return ResultExpectation::ASSERT_NOTHING;
            case 'findOne':
            case 'findOneAndDelete':
            case 'findOneAndReplace':
            case 'findOneAndUpdate':
                return ResultExpectation::ASSERT_SAME_DOCUMENT;
            case 'find':
                return ResultExpectation::ASSERT_SAME_DOCUMENTS;
            case 'insertMany':
                return ResultExpectation::ASSERT_INSERTMANY;
            case 'insertOne':
                return ResultExpectation::ASSERT_INSERTONE;
            case 'listIndexes':
                return ResultExpectation::ASSERT_SAME_DOCUMENTS;
            case 'mapReduce':
                return ResultExpectation::ASSERT_SAME_DOCUMENTS;
            case 'replaceOne':
            case 'updateMany':
            case 'updateOne':
                return ResultExpectation::ASSERT_UPDATE;
            case 'watch':
                return ResultExpectation::ASSERT_SAME_DOCUMENTS;
            default:
                throw new LogicException('Unsupported collection operation: ' . $this->name);
        }
    }

    /**
     * @throws LogicException if the database operation is unsupported
     */
    private function getResultAssertionTypeForDatabase()
    {
        switch ($this->name) {
            case 'aggregate':
            case 'listCollections':
                return ResultExpectation::ASSERT_SAME_DOCUMENTS;
            case 'listCollectionNames':
                return ResultExpectation::ASSERT_SAME;
            case 'createCollection':
            case 'dropCollection':
            case 'runCommand':
                return ResultExpectation::ASSERT_MATCHES_DOCUMENT;
            case 'watch':
                return ResultExpectation::ASSERT_SAME_DOCUMENTS;
            default:
                throw new LogicException('Unsupported database operation: ' . $this->name);
        }
    }

    /**
     * Prepares a request element for a bulkWrite operation.
     *
     * @param stdClass $request
     * @return array
     * @throws LogicException if the bulk write request is unsupported
     */
    private function prepareBulkWriteRequest(stdClass $request)
    {
        $args = (array) $request->arguments;

        switch ($request->name) {
            case 'deleteMany':
            case 'deleteOne':
                return [
                    $request->name => [
                        $args['filter'],
                        array_diff_key($args, ['filter' => 1]),
                    ],
                ];
            case 'insertOne':
                return [ 'insertOne' => [ $args['document'] ]];
            case 'replaceOne':
                return [
                    'replaceOne' => [
                        $args['filter'],
                        $args['replacement'],
                        array_diff_key($args, ['filter' => 1, 'replacement' => 1]),
                    ],
                ];
            case 'updateMany':
            case 'updateOne':
                return [
                    $request->name => [
                        $args['filter'],
                        $args['update'],
                        array_diff_key($args, ['filter' => 1, 'update' => 1]),
                    ],
                ];
            default:
                throw new LogicException('Unsupported bulk write request: ' . $request->name);
        }
    }
}
