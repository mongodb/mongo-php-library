<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\Exception;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Operation\FindOneAndUpdate;
use LogicException;
use stdClass;

/**
 * Spec test operation.
 */
final class Operation
{
    const OBJECT_COLLECTION = 'collection';
    const OBJECT_DATABASE = 'database';
    const OBJECT_SELECT_COLLECTION = 'selectCollection';
    const OBJECT_SELECT_DATABASE = 'selectDatabase';
    const OBJECT_SESSION0 = 'session0';
    const OBJECT_SESSION1 = 'session1';

    public $errorExpectation;
    public $resultExpectation;

    private $arguments = [];
    private $collectionName;
    private $collectionOptions = [];
    private $databaseName;
    private $databaseOptions = [];
    private $name;
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
            $o->arguments = ['command' => [
                'renameCollection' => $operation->database . '.' . $operation->collection,
                'to' => $operation->database . '.' . $operation->arguments->to,
                // Note: Database::command() does not inherit WC, so be explicit
                'writeConcern' => $writeConcern,
            ]];

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

        if (isset($operation->databaseOptions)) {
            $o->databaseOptions = (array) $operation->databaseOptions;
        }

        return $o;
    }

    /**
     * Execute the operation and assert its outcome.
     *
     * @param FunctionalTestCase $test    Test instance
     * @param Context            $context Execution context
     */
    public function assert(FunctionalTestCase $test, Context $context)
    {
        $result = null;
        $exception = null;

        try {
            $result = $this->execute($context);

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
    }

    /**
     * Executes the operation with a given context.
     *
     * @param FunctionalTestCase $test    Test instance
     * @param Context            $context Execution context
     * @return mixed
     * @throws LogicException if the operation is unsupported
     */
    private function execute(Context $context)
    {
        switch ($this->object) {
            case self::OBJECT_COLLECTION:
                $collection = $context->getCollection($this->collectionOptions);
                return $this->executeForCollection($collection, $context);

            case self::OBJECT_DATABASE:
                $database = $context->getDatabase($this->databaseOptions);
                return $this->executeForDatabase($database, $context);

            case self::OBJECT_SELECT_COLLECTION:
                $collection = $context->selectCollection($this->databaseName, $this->collectionName, $this->collectionOptions);
                return $this->executeForCollection($collection, $context);

            case self::OBJECT_SELECT_DATABASE:
                $database = $context->selectDatabase($this->databaseName, $this->databaseOptions);
                return $this->executeForDatabase($database, $context);

            case self::OBJECT_SESSION0:
                return $this->executeForSession($context->session0, $context);

            case self::OBJECT_SESSION1:
                return $this->executeForSession($context->session1, $context);

            default:
                throw new LogicException('Unsupported object: ' . $this->object);
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

            case 'findOneAndReplace':
                if (isset($args['returnDocument'])) {
                    $args['returnDocument'] = ('after' === strtolower($args['returnDocument']))
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
                    $args['returnDocument'] = ('after' === strtolower($args['returnDocument']))
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
            case 'runCommand':
                return $database->command(
                    $args['command'],
                    array_diff_key($args, ['command' => 1])
                )->toArray()[0];

            default:
                throw new LogicException('Unsupported database operation: ' . $this->name);
        }
    }

    /**
     * Executes the session operation and return its result.
     *
     * @param Session $session
     * @param Context $context Execution context
     * @return mixed
     * @throws LogicException if the session operation is unsupported
     */
    private function executeForSession(Session $session, Context $context)
    {
        switch ($this->name) {
            case 'abortTransaction':
                return $session->abortTransaction();

            case 'commitTransaction':
                return $session->commitTransaction();

            case 'startTransaction':
                $options = isset($this->arguments['options']) ? (array) $this->arguments['options'] : [];
                return $session->startTransaction($context->prepareOptions($options));

            default:
                throw new LogicException('Unsupported session operation: ' . $this->name);
        }
    }

    /**
     * @throws LogicException if the operation object is unsupported
     */
    private function getResultAssertionType()
    {
        switch ($this->object) {
            case Operation::OBJECT_COLLECTION:
                return $this->getResultAssertionTypeForCollection();

            case Operation::OBJECT_DATABASE:
                return $this->getResultAssertionTypeForDatabase();

            case Operation::OBJECT_SESSION0:
            case Operation::OBJECT_SESSION1:
                return ResultExpectation::ASSERT_NOTHING;

            default:
                throw new LogicException('Unsupported object: ' . $this->object);
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
                if (\MongoDB\is_last_pipeline_operator_out($this->arguments['pipeline'])) {
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

            case 'replaceOne':
            case 'updateMany':
            case 'updateOne':
                return ResultExpectation::ASSERT_UPDATE;

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
            case 'runCommand':
                return ResultExpectation::ASSERT_MATCHES_DOCUMENT;

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
                return [ $request->name => [
                    $args['filter'],
                    array_diff_key($args, ['filter' => 1]),
                ]];

            case 'insertOne':
                return [ 'insertOne' => [ $args['document'] ]];

            case 'replaceOne':
                return [ 'replaceOne' => [
                    $args['filter'],
                    $args['replacement'],
                    array_diff_key($args, ['filter' => 1, 'replacement' => 1]),
                ]];

            case 'updateMany':
            case 'updateOne':
                return [ $request->name => [
                    $args['filter'],
                    $args['update'],
                    array_diff_key($args, ['filter' => 1, 'update' => 1]),
                ]];

            default:
                throw new LogicException('Unsupported bulk write request: ' . $request->name);
        }
    }
}
