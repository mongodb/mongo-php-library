<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\ChangeStream;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\GridFS\Bucket;
use stdClass;

use function array_diff_key;
use function array_fill_keys;
use function array_key_exists;
use function array_keys;
use function implode;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsBool;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\isInstanceOf;
use function PHPUnit\Framework\isType;
use function PHPUnit\Framework\logicalOr;

final class Util
{
    /**
     * Array to fill, which contains the schema of allowed attributes for operations.
     */
    private static $args = [
        Operation::OBJECT_TEST_RUNNER => [
            'assertCollectionExists' => ['databaseName', 'collectionName'],
            'assertCollectionNotExists' => ['databaseName', 'collectionName'],
            'assertIndexExists' => ['databaseName', 'collectionName', 'indexName'],
            'assertIndexNotExists' => ['databaseName', 'collectionName', 'indexName'],
            'assertSameLsidOnLastTwoCommands' => ['client'],
            'assertDifferentLsidOnLastTwoCommands' => ['client'],
            'assertNumberConnectionsCheckedOut' => ['connections'],
            'assertSessionDirty' => ['session'],
            'assertSessionNotDirty' => ['session'],
            'assertSessionPinned' => ['session'],
            'assertSessionTransactionState' => ['session', 'state'],
            'assertSessionUnpinned' => ['session'],
            'failPoint' => ['client', 'failPoint'],
            'targetedFailPoint' => ['session', 'failPoint'],
            'loop' => ['operations', 'storeErrorsAsEntity', 'storeFailuresAsEntity', 'storeSuccessesAsEntity', 'storeIterationsAsEntity'],
        ],
        Client::class => [
            'createChangeStream' => ['pipeline', 'session', 'fullDocument', 'resumeAfter', 'startAfter', 'startAtOperationTime', 'batchSize', 'collation', 'maxAwaitTimeMS', 'showExpandedEvents'],
            'listDatabaseNames' => ['authorizedDatabases', 'filter', 'maxTimeMS', 'session'],
            'listDatabases' => ['authorizedDatabases', 'filter', 'maxTimeMS', 'session'],
        ],
        ClientEncryption::class => [
            'addKeyAltName' => ['id', 'keyAltName'],
            'createDataKey' => ['kmsProvider', 'opts'],
            'deleteKey' => ['id'],
            'getKey' => ['id'],
            'getKeyByAltName' => ['keyAltName'],
            'getKeys' => [],
            'removeKeyAltName' => ['id', 'keyAltName'],
            'rewrapManyDataKey' => ['filter', 'opts'],
        ],
        Database::class => [
            'aggregate' => ['pipeline', 'session', 'useCursor', 'allowDiskUse', 'batchSize', 'bypassDocumentValidation', 'collation', 'comment', 'explain', 'hint', 'let', 'maxAwaitTimeMS', 'maxTimeMS'],
            'createChangeStream' => ['pipeline', 'session', 'fullDocument', 'resumeAfter', 'startAfter', 'startAtOperationTime', 'batchSize', 'collation', 'maxAwaitTimeMS', 'showExpandedEvents'],
            'createCollection' => ['collection', 'session', 'autoIndexId', 'capped', 'changeStreamPreAndPostImages', 'clusteredIndex', 'collation', 'expireAfterSeconds', 'flags', 'indexOptionDefaults', 'max', 'maxTimeMS', 'pipeline', 'size', 'storageEngine', 'timeseries', 'validationAction', 'validationLevel', 'validator', 'viewOn'],
            'dropCollection' => ['collection', 'session'],
            'listCollectionNames' => ['authorizedCollections', 'filter', 'maxTimeMS', 'session'],
            'listCollections' => ['authorizedCollections', 'filter', 'maxTimeMS', 'session'],
            'modifyCollection' => ['collection', 'changeStreamPreAndPostImages', 'index', 'validator'],
            // Note: commandName is not used by PHP
            'runCommand' => ['command', 'session', 'commandName'],
        ],
        Collection::class => [
            'aggregate' => ['pipeline', 'session', 'useCursor', 'allowDiskUse', 'batchSize', 'bypassDocumentValidation', 'collation', 'comment', 'explain', 'hint', 'let', 'maxAwaitTimeMS', 'maxTimeMS'],
            'bulkWrite' => ['let', 'requests', 'session', 'ordered', 'bypassDocumentValidation', 'comment'],
            'createChangeStream' => ['pipeline', 'session', 'fullDocument', 'fullDocumentBeforeChange', 'resumeAfter', 'startAfter', 'startAtOperationTime', 'batchSize', 'collation', 'maxAwaitTimeMS', 'comment', 'showExpandedEvents'],
            'createFindCursor' => ['filter', 'session', 'allowDiskUse', 'allowPartialResults', 'batchSize', 'collation', 'comment', 'cursorType', 'hint', 'limit', 'max', 'maxAwaitTimeMS', 'maxScan', 'maxTimeMS', 'min', 'modifiers', 'noCursorTimeout', 'oplogReplay', 'projection', 'returnKey', 'showRecordId', 'skip', 'snapshot', 'sort'],
            'createIndex' => ['keys', 'comment', 'commitQuorum', 'maxTimeMS', 'name', 'session', 'unique'],
            'dropIndex' => ['name', 'session', 'maxTimeMS', 'comment'],
            'count' => ['filter', 'session', 'collation', 'hint', 'limit', 'maxTimeMS', 'skip', 'comment'],
            'countDocuments' => ['filter', 'session', 'limit', 'skip', 'collation', 'hint', 'maxTimeMS', 'comment'],
            'estimatedDocumentCount' => ['session', 'maxTimeMS', 'comment'],
            'deleteMany' => ['let', 'filter', 'session', 'collation', 'hint', 'comment'],
            'deleteOne' => ['let', 'filter', 'session', 'collation', 'hint', 'comment'],
            'findOneAndDelete' => ['let', 'filter', 'session', 'projection', 'arrayFilters', 'bypassDocumentValidation', 'collation', 'hint', 'maxTimeMS', 'new', 'sort', 'update', 'upsert', 'comment'],
            'distinct' => ['fieldName', 'filter', 'session', 'collation', 'maxTimeMS', 'comment'],
            'drop' => ['session', 'comment'],
            'find' => ['let', 'filter', 'session', 'allowDiskUse', 'allowPartialResults', 'batchSize', 'collation', 'comment', 'cursorType', 'hint', 'limit', 'max', 'maxAwaitTimeMS', 'maxScan', 'maxTimeMS', 'min', 'modifiers', 'noCursorTimeout', 'oplogReplay', 'projection', 'returnKey', 'showRecordId', 'skip', 'snapshot', 'sort'],
            'findOne' => ['let', 'filter', 'session', 'allowDiskUse', 'allowPartialResults', 'batchSize', 'collation', 'comment', 'cursorType', 'hint', 'max', 'maxAwaitTimeMS', 'maxScan', 'maxTimeMS', 'min', 'modifiers', 'noCursorTimeout', 'oplogReplay', 'projection', 'returnKey', 'showRecordId', 'skip', 'snapshot', 'sort'],
            'findOneAndReplace' => ['let', 'returnDocument', 'filter', 'replacement', 'session', 'projection', 'returnDocument', 'upsert', 'arrayFilters', 'bypassDocumentValidation', 'collation', 'hint', 'maxTimeMS', 'new', 'remove', 'sort', 'comment'],
            'rename' => ['to', 'comment', 'dropTarget'],
            'replaceOne' => ['let', 'filter', 'replacement', 'session', 'upsert', 'arrayFilters', 'bypassDocumentValidation', 'collation', 'hint', 'comment'],
            'findOneAndUpdate' => ['let', 'returnDocument', 'filter', 'update', 'session', 'upsert', 'projection', 'remove', 'arrayFilters', 'bypassDocumentValidation', 'collation', 'hint', 'maxTimeMS', 'sort', 'comment'],
            'updateMany' => ['let', 'filter', 'update', 'session', 'upsert', 'arrayFilters', 'bypassDocumentValidation', 'collation', 'hint', 'comment'],
            'updateOne' => ['let', 'filter', 'update', 'session', 'upsert', 'arrayFilters', 'bypassDocumentValidation', 'collation', 'hint', 'comment'],
            'insertMany' => ['documents', 'session', 'ordered', 'bypassDocumentValidation', 'comment'],
            'insertOne' => ['document', 'session', 'bypassDocumentValidation', 'comment'],
            'listIndexes' => ['session', 'maxTimeMS', 'comment'],
            'mapReduce' => ['map', 'reduce', 'out', 'session', 'bypassDocumentValidation', 'collation', 'finalize', 'jsMode', 'limit', 'maxTimeMS', 'query', 'scope', 'sort', 'verbose', 'comment'],
        ],
        ChangeStream::class => [
            'iterateUntilDocumentOrError' => [],
        ],
        Cursor::class => [
            'close' => [],
            'iterateUntilDocumentOrError' => [],
        ],
        Session::class => [
            'abortTransaction' => [],
            'commitTransaction' => [],
            'endSession' => [],
            'startTransaction' => ['maxCommitTimeMS', 'readConcern', 'readPreference', 'writeConcern'],
            'withTransaction' => ['callback', 'maxCommitTimeMS', 'readConcern', 'readPreference', 'writeConcern'],
        ],
        Bucket::class => [
            'delete' => ['id'],
            'downloadByName' => ['filename', 'revision'],
            'download' => ['id'],
            'uploadWithId' => ['id', 'filename', 'source', 'chunkSizeBytes', 'disableMD5', 'contentType', 'metadata'],
            'upload' => ['filename', 'source', 'chunkSizeBytes', 'disableMD5', 'contentType', 'metadata'],
        ],
    ];

    public static function assertHasOnlyKeys($arrayOrObject, array $keys): void
    {
        assertThat($arrayOrObject, logicalOr(isType('array'), isInstanceOf(stdClass::class)));
        $diff = array_diff_key((array) $arrayOrObject, array_fill_keys($keys, 1));
        assertEmpty($diff, 'Unsupported keys: ' . implode(',', array_keys($diff)));
    }

    public static function assertArgumentsBySchema(string $executingObjectName, string $operation, array $args): void
    {
        assertArrayHasKey($executingObjectName, self::$args);
        assertArrayHasKey($operation, self::$args[$executingObjectName]);
        self::assertHasOnlyKeys($args, self::$args[$executingObjectName][$operation]);
    }

    public static function createReadConcern(stdClass $o): ReadConcern
    {
        self::assertHasOnlyKeys($o, ['level']);

        $level = $o->level ?? null;
        assertIsString($level);

        return new ReadConcern($level);
    }

    public static function createReadPreference(stdClass $o): ReadPreference
    {
        self::assertHasOnlyKeys($o, ['mode', 'tagSets', 'maxStalenessSeconds', 'hedge']);

        $mode = $o->mode ?? null;
        $tagSets = $o->tagSets ?? null;
        $maxStalenessSeconds = $o->maxStalenessSeconds ?? null;
        $hedge = $o->hedge ?? null;

        assertIsString($mode);

        if (isset($tagSets)) {
            assertIsArray($tagSets);
            assertContains('object', $tagSets);
        }

        $options = [];

        if (isset($maxStalenessSeconds)) {
            assertIsInt($maxStalenessSeconds);
            $options['maxStalenessSeconds'] = $maxStalenessSeconds;
        }

        if (isset($hedge)) {
            assertIsObject($hedge);
            $options['hedge'] = $hedge;
        }

        return new ReadPreference($mode, $tagSets, $options);
    }

    public static function createWriteConcern(stdClass $o): WriteConcern
    {
        self::assertHasOnlyKeys($o, ['w', 'wtimeoutMS', 'journal']);

        $w = $o->w ?? -2; /* MONGOC_WRITE_CONCERN_W_DEFAULT */
        $wtimeoutMS = $o->wtimeoutMS ?? 0;
        $journal = $o->journal ?? null;

        assertThat($w, logicalOr(isType('int'), isType('string')));
        assertIsInt($wtimeoutMS);

        $args = [$w, $wtimeoutMS];

        if (isset($journal)) {
            assertIsBool($journal);
            $args[] = $journal;
        }

        return new WriteConcern(...$args);
    }

    public static function prepareCommonOptions(array $options): array
    {
        if (array_key_exists('readConcern', $options)) {
            assertIsObject($options['readConcern']);
            $options['readConcern'] = self::createReadConcern($options['readConcern']);
        }

        if (array_key_exists('readPreference', $options)) {
            assertIsObject($options['readPreference']);
            $options['readPreference'] = self::createReadPreference($options['readPreference']);
        }

        if (array_key_exists('writeConcern', $options)) {
            assertIsObject($options['writeConcern']);
            $options['writeConcern'] = self::createWriteConcern($options['writeConcern']);
        }

        return $options;
    }
}
