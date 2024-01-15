<?php

namespace MongoDB\Tests;

use Generator;

use function bin2hex;
use function getenv;
use function putenv;
use function random_bytes;
use function sprintf;

/** @runTestsInSeparateProcesses */
final class ExamplesTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if ($this->isShardedCluster()) {
            $this->markTestSkipped('Examples are not tested on sharded clusters.');
        }

        if ($this->isApiVersionRequired()) {
            $this->markTestSkipped('Examples are not tested when the server requires specifying an API version.');
        }
    }

    /** @dataProvider provideExamples */
    public function testExamples(string $file, string $expectedOutput): void
    {
        $this->assertExampleOutput($file, $expectedOutput);
    }

    public static function provideExamples(): Generator
    {
        $expectedOutput = <<<'OUTPUT'
{ "_id" : null, "totalCount" : 100, "evenCount" : %d, "oddCount" : %d, "maxValue" : %d, "minValue" : %d }
OUTPUT;

        yield 'aggregate' => [
            'file' => __DIR__ . '/../examples/aggregate.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
%s
%s
%s
%s
%s
%s
%s
%s
OUTPUT;

        yield 'bulk' => [
            'file' => __DIR__ . '/../examples/bulk.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
drop command started
command: %s

drop command %a

insert command started
command: %s

insert command succeeded
reply: %s

update command started
command: %s

update command succeeded
reply: %s

find command started
command: %s

find command succeeded
reply: %s

%s
%s
getMore command started
command: %s

getMore command succeeded
reply: %s

%s
OUTPUT;

        yield 'command_logger' => [
            'file' => __DIR__ . '/../examples/command_logger.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
Read a total of 17888890 bytes
Deleted file with ID: %s
OUTPUT;

        yield 'gridfs_stream' => [
            'file' => __DIR__ . '/../examples/gridfs_stream.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
Inserted file with ID: %s
File contents: Hello world!
Deleted file with ID: %s

OUTPUT;

        yield 'gridfs_upload' => [
            'file' => __DIR__ . '/../examples/gridfs_upload.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
File exists: no
Writing file
File exists: yes
Reading file: Hello, GridFS!
Writing new version of the file
Reading new version of the file: Hello, GridFS! (v2)
Reading previous version of the file: Hello, GridFS!
OUTPUT;

        yield 'gridfs_stream_wrapper' => [
            'file' => __DIR__ . '/../examples/gridfs_stream_wrapper.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
MongoDB\Examples\Persistable\PersistableEntry Object
(
    [id:MongoDB\Examples\Persistable\PersistableEntry:private] => MongoDB\BSON\ObjectId Object
        (
            [oid] => %s
        )

    [name] => alcaeus
    [emails] => Array
        (
            [0] => MongoDB\Examples\Persistable\PersistableEmail Object
                (
                    [type] => work
                    [address] => alcaeus@example.com
                )

            [1] => MongoDB\Examples\Persistable\PersistableEmail Object
                (
                    [type] => private
                    [address] => secret@example.com
                )

        )

)
OUTPUT;

        yield 'persistable' => [
            'file' => __DIR__ . '/../examples/persistable.php',
            'expectedOutput' => $expectedOutput,
        ];

        /* Note: Do not assert output beyond the initial topology events, as it
         * may vary based on the test environment. PHPUnit's matching behavior
         * for "%A" also seems to differ slightly from run-tests.php, otherwise
         * we could assert the final topologyClosed event. */
        $expectedOutput = <<<'OUTPUT'
topologyOpening: %x was opened

topologyChanged: %x changed from Unknown to %s

%A
OUTPUT;

        yield 'sdam_logger' => [
            'file' => __DIR__ . '/../examples/sdam_logger.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
MongoDB\Examples\Typemap\TypeMapEntry Object
(
    [id:MongoDB\Examples\Typemap\TypeMapEntry:private] => MongoDB\BSON\ObjectId Object
        (
            [oid] => %s
        )

    [name:MongoDB\Examples\Typemap\TypeMapEntry:private] => alcaeus
    [emails:MongoDB\Examples\Typemap\TypeMapEntry:private] => Array
        (
            [0] => MongoDB\Examples\Typemap\TypeMapEmail Object
                (
                    [type:MongoDB\Examples\Typemap\TypeMapEmail:private] => work
                    [address:MongoDB\Examples\Typemap\TypeMapEmail:private] => alcaeus@example.com
                )

            [1] => MongoDB\Examples\Typemap\TypeMapEmail Object
                (
                    [type:MongoDB\Examples\Typemap\TypeMapEmail:private] => private
                    [address:MongoDB\Examples\Typemap\TypeMapEmail:private] => secret@example.com
                )

        )

)
OUTPUT;

        yield 'typemap' => [
            'file' => __DIR__ . '/../examples/typemap.php',
            'expectedOutput' => $expectedOutput,
        ];
    }

    /**
     * MongoDB Atlas Search example requires a MongoDB Atlas M10+ cluster with MongoDB 7.0+
     * Tips for insiders: if using a cloud-dev server, append ".mongodb.net" to the MONGODB_URI.
     *
     * @group atlas
     */
    public function testAtlasSearch(): void
    {
        $uri = getenv('MONGODB_URI') ?? '';
        if (! self::isAtlas($uri)) {
            $this->markTestSkipped('Atlas Search examples are only supported on MongoDB Atlas');
        }

        $this->skipIfServerVersion('<', '7.0', 'Atlas Search examples require MongoDB 7.0 or later');

        // Generate random collection name to avoid conflicts with consecutive runs as the index creation is asynchronous
        $collectionName = sprintf('%s.%s', $this->getCollectionName(), bin2hex(random_bytes(5)));
        $databaseName = $this->getDatabaseName();
        $collection = $this->createCollection($databaseName, $collectionName);
        $collection->insertMany([
            ['name' => 'Ribeira Charming Duplex'],
            ['name' => 'Ocean View Bondi Beach'],
            ['name' => 'Luxury ocean view Beach Villa 622'],
            ['name' => 'Ocean & Beach View Condo WBR H204'],
            ['name' => 'Bondi Beach Spacious Studio With Ocean View'],
            ['name' => 'New York City - Upper West Side Apt'],
        ]);
        putenv(sprintf('MONGODB_DATABASE=%s', $databaseName));
        putenv(sprintf('MONGODB_COLLECTION=%s', $collectionName));

        $expectedOutput = <<<'OUTPUT'

Creating the index.
%s
Performing a text search...
 - Ocean View Bondi Beach
 - Luxury ocean view Beach Villa 622
 - Ocean & Beach View Condo WBR H204
 - Bondi Beach Spacious Studio With Ocean View

Enjoy MongoDB Atlas Search!


OUTPUT;

        $this->assertExampleOutput(__DIR__ . '/../examples/atlas_search.php', $expectedOutput);
    }

    public function testChangeStream(): void
    {
        $this->skipIfChangeStreamIsNotSupported();

        $expectedOutput = <<<'OUTPUT'
%s
%s
%s
%s
%s
%s
%s
%s
%s
%s
Aborting after 3 seconds...
OUTPUT;

        $this->assertExampleOutput(__DIR__ . '/../examples/changestream.php', $expectedOutput);
    }

    public function testWithTransaction(): void
    {
        $this->skipIfTransactionsAreNotSupported();

        $expectedOutput = <<<'OUTPUT'
%s
%s
%s
OUTPUT;

        $this->assertExampleOutput(__DIR__ . '/../examples/with_transaction.php', $expectedOutput);
    }

    /** @dataProvider provideEncryptionExamples */
    public function testEncryptionExamples(string $file, string $expectedOutput): void
    {
        $this->skipIfClientSideEncryptionIsNotSupported();

        /* Ensure that the key vault, collection under test, and any metadata
         * collections are cleaned up before and after the example is run. */
        $this->dropCollection('test', 'coll', ['encryptedFields' => []]);
        $this->dropCollection('encryption', '__keyVault');

        /* Ensure the key vault has a partial, unique index for keyAltNames. The
         * key management examples already do this, so this is mainly for the
         * benefit of other scripts. */
        $this->setUpKeyVaultIndex();

        $this->assertExampleOutput($file, $expectedOutput);
    }

    public static function provideEncryptionExamples(): Generator
    {
        $expectedOutput = <<<'OUTPUT'
MongoDB\BSON\Binary Object
(
    [data] => %a
    [type] => 4
)
MongoDB\BSON\Binary Object
(
    [data] => %a
    [type] => 6
)
OUTPUT;

        yield 'create_data_key' => [
            'file' => __DIR__ . '/../docs/examples/encryption/create_data_key.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
Error creating key: E11000 duplicate key error %s: encryption.__keyVault%sdup key: { keyAltNames: "myDataKey" }
MongoDB\BSON\Binary Object
(
    [data] => %a
    [type] => 6
)
OUTPUT;

        yield 'key_alt_name' => [
            'file' => __DIR__ . '/../docs/examples/encryption/key_alt_name.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
MongoDB\Model\BSONDocument Object
(
    [storage:ArrayObject:private] => Array
        (
            [_id] => 1
            [encryptedField] => mySecret
        )

)
MongoDB\Model\BSONDocument Object
(
    [storage:ArrayObject:private] => Array
        (
            [_id] => 1
            [encryptedField] => MongoDB\BSON\Binary Object
                (
                    [data] => %a
                    [type] => 6
                )

        )

)
Error inserting document: Document failed validation
OUTPUT;

        yield 'csfle-automatic_encryption-local_schema' => [
            'file' => __DIR__ . '/../docs/examples/encryption/csfle-automatic_encryption-local_schema.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
MongoDB\Model\BSONDocument Object
(
    [storage:ArrayObject:private] => Array
        (
            [_id] => 1
            [encryptedField] => mySecret
        )

)
MongoDB\Model\BSONDocument Object
(
    [storage:ArrayObject:private] => Array
        (
            [_id] => 1
            [encryptedField] => MongoDB\BSON\Binary Object
                (
                    [data] => %a
                    [type] => 6
                )

        )

)
Error inserting document: Document failed validation
OUTPUT;

        yield 'csfle-automatic_encryption-server_side_schema' => [
            'file' => __DIR__ . '/../docs/examples/encryption/csfle-automatic_encryption-server_side_schema.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
MongoDB\Model\BSONDocument Object
(
    [storage:ArrayObject:private] => Array
        (
            [_id] => 1
            [encryptedField] => MongoDB\BSON\Binary Object
                (
                    [data] => %a
                    [type] => 6
                )

        )

)
Decrypted: mySecret
OUTPUT;

        yield 'csfle-explicit_encryption' => [
            'file' => __DIR__ . '/../docs/examples/encryption/csfle-explicit_encryption.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
MongoDB\Model\BSONDocument Object
(
    [storage:ArrayObject:private] => Array
        (
            [_id] => 1
            [encryptedField] => mySecret
        )

)
OUTPUT;

        yield 'csfle-explicit_encryption_automatic_decryption' => [
            'file' => __DIR__ . '/../docs/examples/encryption/csfle-explicit_encryption_automatic_decryption.php',
            'expectedOutput' => $expectedOutput,
        ];
    }

    /** @dataProvider provideQueryableEncryptionExamples */
    public function testQueryableEncryptionExamples(string $file, string $expectedOutput): void
    {
        $this->skipIfClientSideEncryptionIsNotSupported();

        $this->skipIfServerVersion('<', '7.0.0', 'Queryable encryption tests require MongoDB 7.0 or later');

        if ($this->isStandalone()) {
            $this->markTestSkipped('Queryable encryption requires replica sets');
        }

        /* Ensure that the key vault, collection under test, and any metadata
         * collections are cleaned up before and after the example is run. */
        $this->dropCollection('test', 'coll', ['encryptedFields' => []]);
        $this->dropCollection('encryption', '__keyVault');

        // Ensure the key vault has a partial, unique index for keyAltNames
        $this->setUpKeyVaultIndex();

        $this->assertExampleOutput($file, $expectedOutput);
    }

    public static function provideQueryableEncryptionExamples(): Generator
    {
        $expectedOutput = <<<'OUTPUT'
MongoDB\Model\BSONDocument Object
(
    [storage:ArrayObject:private] => Array
        (
            [_id] => 1
            [encryptedIndexed] => indexedValue
            [encryptedUnindexed] => unindexedValue
            [__safeContent__] => MongoDB\Model\BSONArray Object
                (
                    [storage:ArrayObject:private] => Array
                        (
                            [0] => MongoDB\BSON\Binary Object
                                (
                                    [data] => %a
                                    [type] => 0
                                )

                        )

                )

        )

)
MongoDB\Model\BSONDocument Object
(
    [storage:ArrayObject:private] => Array
        (
            [_id] => 1
            [encryptedIndexed] => MongoDB\BSON\Binary Object
                (
                    [data] => %a
                    [type] => 6
                )

            [encryptedUnindexed] => MongoDB\BSON\Binary Object
                (
                    [data] => %a
                    [type] => 6
                )

            [__safeContent__] => MongoDB\Model\BSONArray Object
                (
                    [storage:ArrayObject:private] => Array
                        (
                            [0] => MongoDB\BSON\Binary Object
                                (
                                    [data] => %a
                                    [type] => 0
                                )

                        )

                )

        )

)
OUTPUT;

        yield 'queryable_encryption-automatic' => [
            'file' => __DIR__ . '/../docs/examples/encryption/queryable_encryption-automatic.php',
            'expectedOutput' => $expectedOutput,
        ];

        $expectedOutput = <<<'OUTPUT'
MongoDB\Model\BSONDocument Object
(
    [storage:ArrayObject:private] => Array
        (
            [_id] => 1
            [encryptedIndexed] => indexedValue
            [encryptedUnindexed] => unindexedValue
            [__safeContent__] => MongoDB\Model\BSONArray Object
                (
                    [storage:ArrayObject:private] => Array
                        (
                            [0] => MongoDB\BSON\Binary Object
                                (
                                    [data] => %a
                                    [type] => 0
                                )

                        )

                )

        )

)
OUTPUT;

        yield 'queryable_encryption-explicit' => [
            'file' => __DIR__ . '/../docs/examples/encryption/queryable_encryption-explicit.php',
            'expectedOutput' => $expectedOutput,
        ];
    }

    private function assertExampleOutput(string $file, string $expectedOutput): void
    {
        require $file;

        $this->assertStringMatchesFormat($expectedOutput, $this->getActualOutputForAssertion());
    }

    private function setUpKeyVaultIndex(): void
    {
        self::createTestClient()->selectCollection('encryption', '__keyVault')->createIndex(
            ['keyAltNames' => 1],
            [
                'unique' => true,
                'partialFilterExpression' => ['keyAltNames' => ['$exists' => true]],
            ],
        );
    }
}
