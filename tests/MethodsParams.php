<?php

namespace MongoDB\Tests;

use MongoDB\ChangeStream;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Session;
use MongoDB\GridFS\Bucket;
use MongoDB\Tests\UnifiedSpecTests\Operation;

final class MethodsParams
{
    /**
     * Array to fill, which contains the schema of allowed attributes for operations.
     */
    public static $args = [
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
            'failPoint' => ['client', 'failPoint', ''],
            'targetedFailPoint' => ['session', 'failPoint' ],
//            'loop' => [],
        ],
        Client::class => [],
        Database::class => [],
        Collection::class => [],
        ChangeStream::class => [],
        Cursor::class => [],
        Session::class => [],
        Bucket::class => [],
    ];
}
