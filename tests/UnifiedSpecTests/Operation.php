<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\ChangeStream;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\GridFS\Bucket;
use MongoDB\Model\IndexInfo;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Operation\FindOneAndUpdate;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use stdClass;
use Throwable;
use Traversable;
use function array_diff_key;
use function array_key_exists;
use function array_map;
use function current;
use function fopen;
use function fwrite;
use function get_class;
use function hex2bin;
use function iterator_to_array;
use function key;
use function MongoDB\with_transaction;
use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function PHPUnit\Framework\assertNotContains;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertObjectHasAttribute;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\logicalOr;
use function property_exists;
use function rewind;
use function stream_get_contents;
use function strtolower;

final class Operation
{
    const OBJECT_TEST_RUNNER = 'testRunner';

    /** @var bool */
    private $isTestRunnerOperation;

    /** @var string */
    private $name;

    /** @var ?string */
    private $object;

    /** @var array */
    private $arguments = [];

    /** @var Context */
    private $context;

    /** @var EntityMap */
    private $entityMap;

    /** @var ExpectedError */
    private $expectedError;

    /** @var ExpectedResult */
    private $expectedResult;

    /** @var string */
    private $saveResultAsEntity;

    public function __construct(stdClass $o, Context $context)
    {
        $this->context =$context;
        $this->entityMap = $context->getEntityMap();

        assertIsString($o->name);
        $this->name = $o->name;

        assertIsString($o->object);
        $this->isTestRunnerOperation = $o->object === self::OBJECT_TEST_RUNNER;
        $this->object = $this->isTestRunnerOperation ? null : $o->object;

        if (isset($o->arguments)) {
            assertIsObject($o->arguments);
            $this->arguments = (array) $o->arguments;
        }

        if (isset($o->expectError) && (property_exists($o, 'expectResult') || isset($o->saveResultAsEntity))) {
            Assert::fail('expectError is mutually exclusive with expectResult and saveResultAsEntity');
        }

        $this->expectError = new ExpectedError($o->expectError ?? null, $this->entityMap);
        $this->expectResult = new ExpectedResult($o, $this->entityMap, $this->object);

        if (isset($o->saveResultAsEntity)) {
            assertIsString($o->saveResultAsEntity);
            $this->saveResultAsEntity = $o->saveResultAsEntity;
        }
    }

    /**
     * Execute the operation and assert its outcome.
     */
    public function assert(bool $rethrowExceptions = false)
    {
        $error = null;
        $result = null;
        $saveResultAsEntity = null;

        if (isset($this->arguments['session'])) {
            $dirtySessionObserver = new DirtySessionObserver($this->entityMap->getLogicalSessionId($this->arguments['session']));
            $dirtySessionObserver->start();
        }

        try {
            $result = $this->execute();
            $saveResultAsEntity = $this->saveResultAsEntity;
        } catch (Throwable $e) {
            /* Note: we must be selective about what PHPUnit exceptions to pass
             * through, as PHPUnit's Warning exception must be considered for
             * expectError in GridFS tests (see: PHPLIB-592).
             *
             * TODO: Consider adding operation details (e.g. operations[] index)
             * to the exception message. Alternatively, throw a new exception
             * and include this as the previous, since PHPUnit will render the
             * chain when reporting a test failure. */
            if ($e instanceof AssertionFailedError) {
                throw $e;
            }

            $error = $e;
        }

        if (isset($dirtySessionObserver)) {
            $dirtySessionObserver->stop();

            if ($dirtySessionObserver->observedNetworkError()) {
                $this->context->markDirtySession($this->arguments['session']);
            }
        }

        $this->expectError->assert($error);
        $this->expectResult->assert($result, $saveResultAsEntity);

        // Rethrowing is primarily used for withTransaction callbacks
        if ($error && $rethrowExceptions) {
            throw $error;
        }
    }

    private function execute()
    {
        $this->context->setActiveClient(null);

        if ($this->isTestRunnerOperation) {
            return $this->executeForTestRunner();
        }

        $object = $this->entityMap[$this->object];
        assertIsObject($object);

        $this->context->setActiveClient($this->entityMap->getRootClientIdOf($this->object));

        switch (get_class($object)) {
            case Client::class:
                $result = $this->executeForClient($object);
                break;
            case Database::class:
                $result = $this->executeForDatabase($object);
                break;
            case Collection::class:
                $result = $this->executeForCollection($object);
                break;
            case ChangeStream::class:
                $result = $this->executeForChangeStream($object);
                break;
            case Session::class:
                $result = $this->executeForSession($object);
                break;
            case Bucket::class:
                $result = $this->executeForBucket($object);
                break;
            default:
                Assert::fail('Unsupported entity type: ' . get_class($object));
        }

        if ($result instanceof Traversable && ! $result instanceof ChangeStream) {
            return iterator_to_array($result);
        }

        return $result;
    }

    private function executeForChangeStream(ChangeStream $changeStream)
    {
        $args = $this->prepareArguments();

        switch ($this->name) {
            case 'iterateUntilDocumentOrError':
                /* Note: the first iteration should use rewind, otherwise we may
                 * miss a document from the initial batch (possible if using a
                 * resume token). We can infer this from a null key; however,
                 * if a test ever calls this operation consecutively to expect
                 * multiple errors from the same ChangeStream we will need a
                 * different approach (e.g. examining internal hasAdvanced
                 * property on the ChangeStream). */
                if ($changeStream->key() === null) {
                    $changeStream->rewind();

                    if ($changeStream->valid()) {
                        return $changeStream->current();
                    }
                }

                do {
                    $changeStream->next();
                } while (! $changeStream->valid());

                return $changeStream->current();
            default:
                Assert::fail('Unsupported client operation: ' . $this->name);
        }
    }

    private function executeForClient(Client $client)
    {
        $args = $this->prepareArguments();

        switch ($this->name) {
            case 'createChangeStream':
                $changeStream = $client->watch(
                    $args['pipeline'] ?? [],
                    array_diff_key($args, ['pipeline' => 1])
                );
                $changeStream->rewind();

                return $changeStream;
            case 'listDatabaseNames':
                return $client->listDatabaseNames($args);
            case 'listDatabases':
                return $client->listDatabases($args);
            default:
                Assert::fail('Unsupported client operation: ' . $this->name);
        }
    }

    private function executeForCollection(Collection $collection)
    {
        $args = $this->prepareArguments();

        switch ($this->name) {
            case 'aggregate':
                return $collection->aggregate(
                    $args['pipeline'],
                    array_diff_key($args, ['pipeline' => 1])
                );
            case 'bulkWrite':
                return $collection->bulkWrite(
                    array_map('self::prepareBulkWriteRequest', $args['requests']),
                    array_diff_key($args, ['requests' => 1])
                );
            case 'createChangeStream':
                $changeStream = $collection->watch(
                    $args['pipeline'] ?? [],
                    array_diff_key($args, ['pipeline' => 1])
                );
                $changeStream->rewind();

                return $changeStream;
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
                if (isset($args['session']) && $args['session']->isInTransaction()) {
                    // Transaction, but sharded cluster?
                    $collection->distinct('foo');
                }

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
                    $args['returnDocument'] = strtolower($args['returnDocument']);
                    assertThat($args['returnDocument'], logicalOr(equalTo('after'), equalTo('before')));

                    $args['returnDocument'] = 'after' === $args['returnDocument']
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
                    $args['returnDocument'] = strtolower($args['returnDocument']);
                    assertThat($args['returnDocument'], logicalOr(equalTo('after'), equalTo('before')));

                    $args['returnDocument'] = 'after' === $args['returnDocument']
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
            default:
                Assert::fail('Unsupported collection operation: ' . $this->name);
        }
    }

    private function executeForDatabase(Database $database)
    {
        $args = $this->prepareArguments();

        switch ($this->name) {
            case 'aggregate':
                return $database->aggregate(
                    $args['pipeline'],
                    array_diff_key($args, ['pipeline' => 1])
                );
            case 'createChangeStream':
                $changeStream = $database->watch(
                    $args['pipeline'] ?? [],
                    array_diff_key($args, ['pipeline' => 1])
                );
                $changeStream->rewind();

                return $changeStream;
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
                return $database->listCollectionNames($args);
            case 'listCollections':
                return $database->listCollections($args);
            case 'runCommand':
                return $database->command(
                    $args['command'],
                    array_diff_key($args, ['command' => 1])
                )->toArray()[0];
            default:
                Assert::fail('Unsupported database operation: ' . $this->name);
        }
    }

    private function executeForSession(Session $session)
    {
        $args = $this->prepareArguments();

        switch ($this->name) {
            case 'abortTransaction':
                return $session->abortTransaction();
            case 'commitTransaction':
                return $session->commitTransaction();
            case 'endSession':
                return $session->endSession();
            case 'startTransaction':
                return $session->startTransaction($args);
            case 'withTransaction':
                assertIsArray($args['callback']);

                $operations = array_map(function ($o) {
                    assertIsObject($o);

                    return new Operation($o, $this->context);
                }, $args['callback']);

                $callback = function () use ($operations) {
                    foreach ($operations as $operation) {
                        $operation->assert(true); // rethrow exceptions
                    }
                };

                return with_transaction($session, $callback, array_diff_key($args, ['callback' => 1]));
            default:
                Assert::fail('Unsupported session operation: ' . $this->name);
        }
    }

    private function executeForBucket(Bucket $bucket)
    {
        $args = $this->prepareArguments();

        switch ($this->name) {
            case 'delete':
                return $bucket->delete($args['id']);
            case 'downloadByName':
                return stream_get_contents($bucket->openDownloadStream(
                    $args['filename'],
                    array_diff_key($args, ['filename' => 1])
                ));
            case 'download':
                return stream_get_contents($bucket->openDownloadStream($args['id']));
            case 'uploadWithId':
                $args['_id'] = $args['id'];
                unset($args['id']);

                // Fall through

            case 'upload':
                $args = self::prepareUploadArguments($args);

                return $bucket->uploadFromStream(
                    $args['filename'],
                    $args['source'],
                    array_diff_key($args, ['filename' => 1, 'source' => 1])
                );
            default:
                Assert::fail('Unsupported bucket operation: ' . $this->name);
        }
    }

    private function executeForTestRunner()
    {
        $args = $this->prepareArguments();

        if (array_key_exists('client', $args)) {
            assertIsString($args['client']);
            $args['client'] = $this->entityMap->getClient($args['client']);
        }

        switch ($this->name) {
            case 'assertCollectionExists':
                assertIsString($args['databaseName']);
                assertIsString($args['collectionName']);
                $database = $this->context->getInternalClient()->selectDatabase($args['databaseName']);
                assertContains($args['collectionName'], $database->listCollectionNames());
                break;
            case 'assertCollectionNotExists':
                assertIsString($args['databaseName']);
                assertIsString($args['collectionName']);
                $database = $this->context->getInternalClient()->selectDatabase($args['databaseName']);
                assertNotContains($args['collectionName'], $database->listCollectionNames());
                break;
            case 'assertIndexExists':
                assertIsString($args['databaseName']);
                assertIsString($args['collectionName']);
                assertIsString($args['indexName']);
                assertContains($args['indexName'], $this->getIndexNames($args['databaseName'], $args['collectionName']));
                break;
            case 'assertIndexNotExists':
                assertIsString($args['databaseName']);
                assertIsString($args['collectionName']);
                assertIsString($args['indexName']);
                assertNotContains($args['indexName'], $this->getIndexNames($args['databaseName'], $args['collectionName']));
                break;
            case 'assertSameLsidOnLastTwoCommands':
                $eventObserver = $this->context->getEventObserverForClient($this->arguments['client']);
                assertEquals(...$eventObserver->getLsidsOnLastTwoCommands());
                break;
            case 'assertDifferentLsidOnLastTwoCommands':
                $eventObserver = $this->context->getEventObserverForClient($this->arguments['client']);
                assertNotEquals(...$eventObserver->getLsidsOnLastTwoCommands());
                break;
            case 'assertSessionDirty':
                assertTrue($this->context->isDirtySession($this->arguments['session']));
                break;
            case 'assertSessionNotDirty':
                assertFalse($this->context->isDirtySession($this->arguments['session']));
                break;
            case 'assertSessionPinned':
                assertInstanceOf(Session::class, $args['session']);
                assertInstanceOf(Server::class, $args['session']->getServer());
                break;
            case 'assertSessionTransactionState':
                assertInstanceOf(Session::class, $args['session']);
                assertSame($this->arguments['state'], $args['session']->getTransactionState());
                break;
            case 'assertSessionUnpinned':
                assertInstanceOf(Session::class, $args['session']);
                assertNull($args['session']->getServer());
                break;
            case 'failPoint':
                assertInstanceOf(stdClass::class, $args['failPoint']);
                $args['client']->selectDatabase('admin')->command($args['failPoint']);
                break;
            case 'targetedFailPoint':
                assertInstanceOf(Session::class, $args['session']);
                assertInstanceOf(stdClass::class, $args['failPoint']);
                assertNotNull($args['session']->getServer(), 'Session is pinned');
                $operation = new DatabaseCommand('admin', $args['failPoint']);
                $operation->execute($args['session']->getServer());
                break;
            default:
                Assert::fail('Unsupported test runner operation: ' . $this->name);
        }
    }

    private function getIndexNames(string $databaseName, string $collectionName) : array
    {
        return array_map(
            function (IndexInfo $indexInfo) {
                return $indexInfo->getName();
            },
            iterator_to_array($this->context->getInternalClient()->selectCollection($databaseName, $collectionName)->listIndexes())
        );
    }

    private function prepareArguments() : array
    {
        $args = $this->arguments;

        if (array_key_exists('session', $args)) {
            assertIsString($args['session']);
            $args['session'] = $this->entityMap->getSession($args['session']);
        }

        // Prepare readConcern, readPreference, and writeConcern
        return Util::prepareCommonOptions($args);
    }

    private static function prepareBulkWriteRequest(stdClass $request) : array
    {
        $request = (array) $request;
        assertCount(1, $request);

        $type = key($request);
        $args = current($request);
        assertIsObject($args);
        $args = (array) $args;

        switch ($type) {
            case 'deleteMany':
            case 'deleteOne':
                return [
                    $type => [
                        $args['filter'],
                        array_diff_key($args, ['filter' => 1]),
                    ],
                ];
            case 'insertOne':
                return [ 'insertOne' => [ $args['document']]];
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
                    $type => [
                        $args['filter'],
                        $args['update'],
                        array_diff_key($args, ['filter' => 1, 'update' => 1]),
                    ],
                ];
            default:
                Assert::fail('Unsupported bulk write request: ' . $type);
        }
    }

    private static function prepareUploadArguments(array $args) : array
    {
        $source = $args['source'] ?? null;
        assertIsObject($source);
        assertObjectHasAttribute('$$hexBytes', $source);
        Util::assertHasOnlyKeys($source, ['$$hexBytes']);
        $hexBytes = $source->{'$$hexBytes'};
        assertIsString($hexBytes);
        assertMatchesRegularExpression('/^([0-9a-fA-F]{2})*$/', $hexBytes);

        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, hex2bin($hexBytes));
        rewind($stream);

        $args['source'] = $stream;

        return $args;
    }
}
