<?php

namespace MongoDB\Tests\SpecTests;

use LogicException;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\Exception;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\GridFS\Bucket;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Operation\FindOneAndUpdate;
use stdClass;
use function array_diff_key;
use function array_map;
use function fclose;
use function fopen;
use function in_array;
use function MongoDB\is_last_pipeline_operator_write;
use function MongoDB\with_transaction;
use function stream_get_contents;
use function strtolower;

/**
 * Spec test operation.
 */
final class Operation
{
    const OBJECT_CLIENT = 'client';
    const OBJECT_COLLECTION = 'collection';
    const OBJECT_DATABASE = 'database';
    const OBJECT_GRIDFS_BUCKET = 'gridfsbucket';
    const OBJECT_SELECT_COLLECTION = 'selectCollection';
    const OBJECT_SELECT_DATABASE = 'selectDatabase';
    const OBJECT_SESSION0 = 'session0';
    const OBJECT_SESSION1 = 'session1';
    const OBJECT_TEST_RUNNER = 'testRunner';

    /** @var ErrorExpectation|null */
    public $errorExpectation;

    /** @var ResultExpectation|null */
    public $resultExpectation;

    /** @var array */
    private $arguments = [];

    /** @var string|null */
    private $collectionName;

    /** @var array */
    private $collectionOptions = [];

    /** @var string|null */
    private $databaseName;

    /** @var string */
    private $name;

    /** @var string */
    private $object = self::OBJECT_COLLECTION;

    private function __construct(stdClass $operation)
    {
        $this->name = $operation->name;

        if (isset($operation->arguments)) {
            $this->arguments = (array) $operation->arguments;
        }

        if (isset($operation->object)) {
            $this->object = $operation->object;
        }
    }

    public static function fromChangeStreams(stdClass $operation)
    {
        $o = new self($operation);

        /* Note: change streams only return majority-committed writes, so ensure
         * each operation applies that write concern. This will avoid spurious
         * test failures. */
        $writeConcern = new WriteConcern(WriteConcern::MAJORITY);

        // Expect all operations to succeed
        $o->errorExpectation = ErrorExpectation::noError();

        /* The Change Streams spec tests include a unique "rename" operation,
         * which we should convert to a renameCollection command to be run
         * against the admin database. */
        if ($operation->name === 'rename') {
            $o->object = self::OBJECT_SELECT_DATABASE;
            $o->databaseName = 'admin';
            $o->name = 'runCommand';
            $o->arguments = [
                'command' => [
                    'renameCollection' => $operation->database . '.' . $operation->collection,
                    'to' => $operation->database . '.' . $operation->arguments->to,
                // Note: Database::command() does not inherit WC, so be explicit
                    'writeConcern' => $writeConcern,
                ],
            ];

            return $o;
        }

        $o->databaseName = $operation->database;
        $o->collectionName = $operation->collection;
        $o->collectionOptions = ['writeConcern' => $writeConcern];
        $o->object = self::OBJECT_SELECT_COLLECTION;

        return $o;
    }

    public static function fromCommandMonitoring(stdClass $operation)
    {
        $o = new self($operation);

        if (isset($operation->collectionOptions)) {
            $o->collectionOptions = (array) $operation->collectionOptions;
        }

        /* We purposefully avoid setting a default error expectation, because
         * some tests may trigger a write or command error. */

        return $o;
    }

    /**
     * This method is exclusively used to prepare nested operations for the
     * withTransaction session operation
     *
     * @return Operation
     */
    private static function fromConvenientTransactions(stdClass $operation)
    {
        $o = new self($operation);

        if (isset($operation->error)) {
            $o->errorExpectation = ErrorExpectation::fromTransactions($operation);
        }

        $o->resultExpectation = ResultExpectation::fromTransactions($operation, $o->getResultAssertionType());

        if (isset($operation->collectionOptions)) {
            $o->collectionOptions = (array) $operation->collectionOptions;
        }

        return $o;
    }

    public static function fromCrud(stdClass $operation)
    {
        $o = new self($operation);

        $o->resultExpectation = ResultExpectation::fromCrud($operation, $o->getResultAssertionType());

        if (isset($operation->collectionOptions)) {
            $o->collectionOptions = (array) $operation->collectionOptions;
        }

        return $o;
    }

    public static function fromRetryableReads(stdClass $operation)
    {
        $o = new self($operation);

        $o->errorExpectation = ErrorExpectation::fromRetryableReads($operation);
        $o->resultExpectation = ResultExpectation::fromRetryableReads($operation, $o->getResultAssertionType());

        return $o;
    }

    public static function fromRetryableWrites(stdClass $operation, stdClass $outcome)
    {
        $o = new self($operation);

        $o->errorExpectation = ErrorExpectation::fromRetryableWrites($outcome);
        $o->resultExpectation = ResultExpectation::fromRetryableWrites($outcome, $o->getResultAssertionType());

        return $o;
    }

    public static function fromTransactions(stdClass $operation)
    {
        $o = new self($operation);

        $o->errorExpectation = ErrorExpectation::fromTransactions($operation);
        $o->resultExpectation = ResultExpectation::fromTransactions($operation, $o->getResultAssertionType());

        if (isset($operation->collectionOptions)) {
            $o->collectionOptions = (array) $operation->collectionOptions;
        }

        return $o;
    }

    /**
     * Execute the operation and assert its outcome.
     *
     * @param FunctionalTestCase $test             Test instance
     * @param Context            $context          Execution context
     * @param bool               $bubbleExceptions If true, any exception that was caught is rethrown
     */
    public function assert(FunctionalTestCase $test, Context $context, $bubbleExceptions = false)
    {
        $result = null;
        $exception = null;

        try {
            $result = $this->execute($test, $context);

            /* Eagerly iterate the results of a cursor. This both allows an
             * exception to be thrown sooner and ensures that any expected
             * getMore command(s) can be observed even if a ResultExpectation
             * is not used (e.g. Command Monitoring spec). */
            if ($result instanceof Cursor) {
                $result = $result->toArray();
            }
        } catch (Exception $e) {
            $exception = $e;
        }

        // Extract incomplete result for failed bulkWrite and insertMany operations
        if ($exception instanceof BulkWriteException) {
            $result = $exception->getWriteResult();
        }

        if (isset($this->errorExpectation)) {
            $this->errorExpectation->assert($test, $exception);
        }

        if (isset($this->resultExpectation)) {
            $this->resultExpectation->assert($test, $result);
        }

        if ($exception && $bubbleExceptions) {
            throw $exception;
        }
    }

    /**
     * Executes the operation with a given context.
     *
     * @param Context $context Execution context
     * @return mixed
     * @throws LogicException if the operation is unsupported
     */
    private function execute(FunctionalTestCase $test, Context $context)
    {
        switch ($this->object) {
            case self::OBJECT_CLIENT:
                $client = $context->getClient();

                return $this->executeForClient($client, $context);
            case self::OBJECT_COLLECTION:
                $collection = $context->getCollection($this->collectionOptions);

                return $this->executeForCollection($collection, $context);
            case self::OBJECT_DATABASE:
                $database = $context->getDatabase();

                return $this->executeForDatabase($database, $context);
            case self::OBJECT_GRIDFS_BUCKET:
                $bucket = $context->getGridFSBucket();

                return $this->executeForGridFSBucket($bucket, $context);
            case self::OBJECT_SELECT_COLLECTION:
                $collection = $context->selectCollection($this->databaseName, $this->collectionName, $this->collectionOptions);

                return $this->executeForCollection($collection, $context);
            case self::OBJECT_SELECT_DATABASE:
                $database = $context->selectDatabase($this->databaseName);

                return $this->executeForDatabase($database, $context);
            case self::OBJECT_SESSION0:
                return $this->executeForSession($context->session0, $test, $context);
            case self::OBJECT_SESSION1:
                return $this->executeForSession($context->session1, $test, $context);
            case self::OBJECT_TEST_RUNNER:
                return $this->executeForTestRunner($test, $context);
            default:
                throw new LogicException('Unsupported object: ' . $this->object);
        }
    }

    /**
     * Executes the client operation and return its result.
     *
     * @param Client  $client
     * @param Context $context Execution context
     * @return mixed
     * @throws LogicException if the collection operation is unsupported
     */
    private function executeForClient(Client $client, Context $context)
    {
        $args = $context->prepareOptions($this->arguments);
        $context->replaceArgumentSessionPlaceholder($args);

        switch ($this->name) {
            case 'listDatabases':
                return $client->listDatabases($args);
            case 'watch':
                return $client->watch(
                    isset($args['pipeline']) ? $args['pipeline'] : [],
                    array_diff_key($args, ['pipeline' => 1])
                );
            default:
                throw new LogicException('Unsupported client operation: ' . $this->name);
        }
    }

    /**
     * Executes the collection operation and return its result.
     *
     * @param Collection $collection
     * @param Context    $context    Execution context
     * @return mixed
     * @throws LogicException if the collection operation is unsupported
     */
    private function executeForCollection(Collection $collection, Context $context)
    {
        $args = $context->prepareOptions($this->arguments);
        $context->replaceArgumentSessionPlaceholder($args);

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
            case 'count':
            case 'countDocuments':
            case 'find':
                return $collection->{$this->name}(
                    isset($args['filter']) ? $args['filter'] : [],
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
                    isset($args['filter']) ? $args['filter'] : [],
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
                    isset($args['pipeline']) ? $args['pipeline'] : [],
                    array_diff_key($args, ['pipeline' => 1])
                );
            default:
                throw new LogicException('Unsupported collection operation: ' . $this->name);
        }
    }

    /**
     * Executes the database operation and return its result.
     *
     * @param Database $database
     * @param Context  $context  Execution context
     * @return mixed
     * @throws LogicException if the database operation is unsupported
     */
    private function executeForDatabase(Database $database, Context $context)
    {
        $args = $context->prepareOptions($this->arguments);
        $context->replaceArgumentSessionPlaceholder($args);

        switch ($this->name) {
            case 'aggregate':
                return $database->aggregate(
                    $args['pipeline'],
                    array_diff_key($args, ['pipeline' => 1])
                );
            case 'listCollections':
                return $database->listCollections($args);
            case 'runCommand':
                return $database->command(
                    $args['command'],
                    array_diff_key($args, ['command' => 1])
                )->toArray()[0];
            case 'watch':
                return $database->watch(
                    isset($args['pipeline']) ? $args['pipeline'] : [],
                    array_diff_key($args, ['pipeline' => 1])
                );
            default:
                throw new LogicException('Unsupported database operation: ' . $this->name);
        }
    }

    /**
     * Executes the GridFS bucket operation and return its result.
     *
     * @param Bucket  $bucket
     * @param Context $context Execution context
     * @return mixed
     * @throws LogicException if the database operation is unsupported
     */
    private function executeForGridFSBucket(Bucket $bucket, Context $context)
    {
        $args = $context->prepareOptions($this->arguments);
        $context->replaceArgumentSessionPlaceholder($args);

        switch ($this->name) {
            case 'download':
                $stream = fopen('php://memory', 'w+');
                try {
                    $bucket->downloadToStream($args['id'], $stream);

                    return stream_get_contents($stream);
                } finally {
                    fclose($stream);
                }
                break;

            case 'download_by_name':
                $stream = fopen('php://memory', 'w+');
                try {
                    $bucket->downloadToStreamByName($args['filename'], $stream, array_diff_key($args, ['filename' => 1]));

                    return stream_get_contents($stream);
                } finally {
                    fclose($stream);
                }
                break;

            default:
                throw new LogicException('Unsupported GridFS bucket operation: ' . $this->name);
        }
    }

    /**
     * Executes the session operation and return its result.
     *
     * @param Session            $session
     * @param FunctionalTestCase $test
     * @param Context            $context Execution context
     * @return mixed
     * @throws LogicException if the session operation is unsupported
     */
    private function executeForSession(Session $session, FunctionalTestCase $test, Context $context)
    {
        switch ($this->name) {
            case 'abortTransaction':
                return $session->abortTransaction();
            case 'commitTransaction':
                return $session->commitTransaction();
            case 'startTransaction':
                $options = isset($this->arguments['options']) ? (array) $this->arguments['options'] : [];

                return $session->startTransaction($context->prepareOptions($options));
            case 'withTransaction':
                /** @var self[] $callbackOperations */
                $callbackOperations = array_map(function ($operation) {
                    return self::fromConvenientTransactions($operation);
                }, $this->arguments['callback']->operations);

                $callback = function () use ($callbackOperations, $test, $context) {
                    foreach ($callbackOperations as $operation) {
                        $operation->assert($test, $context, true);
                    }
                };

                $options = isset($this->arguments['options']) ? (array) $this->arguments['options'] : [];

                return with_transaction($session, $callback, $context->prepareOptions($options));
            default:
                throw new LogicException('Unsupported session operation: ' . $this->name);
        }
    }

    private function executeForTestRunner(FunctionalTestCase $test, Context $context)
    {
        $args = $context->prepareOptions($this->arguments);
        $context->replaceArgumentSessionPlaceholder($args);

        switch ($this->name) {
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
                $expected = in_array($this->arguments['state'], ['in_progress', 'starting']);
                $test->assertSame($expected, $args['session']->isInTransaction());

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
