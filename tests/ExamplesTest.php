<?php

namespace MongoDB\Tests;

use Generator;
use MongoDB\Client;

use function getenv;

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

        self::createTestClient()->dropDatabase('test');
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
MongoDB\Examples\PersistableEntry Object
(
    [id:MongoDB\Examples\PersistableEntry:private] => MongoDB\BSON\ObjectId Object
        (
            [oid] => %s
        )

    [name] => alcaeus
    [emails] => Array
        (
            [0] => MongoDB\Examples\PersistableEmail Object
                (
                    [type] => work
                    [address] => alcaeus@example.com
                )

            [1] => MongoDB\Examples\PersistableEmail Object
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
MongoDB\Examples\TypeMapEntry Object
(
    [id:MongoDB\Examples\TypeMapEntry:private] => MongoDB\BSON\ObjectId Object
        (
            [oid] => %s
        )

    [name:MongoDB\Examples\TypeMapEntry:private] => alcaeus
    [emails:MongoDB\Examples\TypeMapEntry:private] => Array
        (
            [0] => MongoDB\Examples\TypeMapEmail Object
                (
                    [type:MongoDB\Examples\TypeMapEmail:private] => work
                    [address:MongoDB\Examples\TypeMapEmail:private] => alcaeus@example.com
                )

            [1] => MongoDB\Examples\TypeMapEmail Object
                (
                    [type:MongoDB\Examples\TypeMapEmail:private] => private
                    [address:MongoDB\Examples\TypeMapEmail:private] => secret@example.com
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
     * MongoDB Atlas Search example requires a MongoDB Atlas M10+ cluster with MongoDB 7.0+ and sample data loaded.
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

        $client = new Client($uri);
        $collection = $client->selectCollection('sample_airbnb', 'listingsAndReviews');
        $count = $collection->estimatedDocumentCount();
        if ($count === 0) {
            $this->markTestSkipped('Atlas Search examples require the sample_airbnb database with the listingsAndReviews collection');
        }

        // Clean variables to avoid conflict with example
        unset($uri, $client, $collection, $count);

        require __DIR__ . '/../examples/atlas-search.php';

        $output = $this->getActualOutputForAssertion();
        $this->assertStringContainsString("\nCreating the index.\n...", $output);
        $this->assertStringContainsString("\nPerforming a text search...\n - ", $output);
        $this->assertStringContainsString("\nEnjoy MongoDB Atlas Search!\n", $output);
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
            'file' => __DIR__ . '/../docs/examples/create_data_key.php',
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
            'file' => __DIR__ . '/../docs/examples/key_alt_name.php',
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
            'file' => __DIR__ . '/../docs/examples/csfle-automatic_encryption-local_schema.php',
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
            'file' => __DIR__ . '/../docs/examples/csfle-automatic_encryption-server_side_schema.php',
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
            'file' => __DIR__ . '/../docs/examples/csfle-explicit_encryption.php',
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
            'file' => __DIR__ . '/../docs/examples/csfle-explicit_encryption_automatic_decryption.php',
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
            'file' => __DIR__ . '/../docs/examples/queryable_encryption-automatic.php',
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
            'file' => __DIR__ . '/../docs/examples/queryable_encryption-explicit.php',
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
