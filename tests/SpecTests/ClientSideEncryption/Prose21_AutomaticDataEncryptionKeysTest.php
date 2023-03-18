<?php

namespace MongoDB\Tests\SpecTests\ClientSideEncryption;

use MongoDB\BSON\Binary;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Exception\InvalidArgumentException;

use function base64_decode;
use function version_compare;

/**
 * Prose test 21: Automatic Data Encryption Keys
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#automatic-data-encryption-keys
 * @group csfle
 * @group serverless
 */
class Prose21_AutomaticDataEncryptionKeysTest extends FunctionalTestCase
{
    public const SERVER_ERROR_TYPEMISMATCH = 14;

    private $clientEncryption;
    private $database;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->isStandalone() || ($this->isShardedCluster() && ! $this->isShardedClusterUsingReplicasets())) {
            $this->markTestSkipped('Automatic data encryption key tests require replica sets');
        }

        if (version_compare($this->getServerVersion(), '6.0.0', '<')) {
            $this->markTestSkipped('Automatic data encryption key tests require MongoDB 6.0 or later');
        }

        $client = static::createTestClient();
        $this->database = $client->selectDatabase($this->getDatabaseName());

        /* Since test cases may create encrypted collections, ensure that the
         * collection and any "enxcol_" collections do not exist. Specify
         * "encryptedFields" to ensure "enxcol_" collections are handled. */
        $this->database->dropCollection($this->getCollectionName(), ['encryptedFields' => []]);

        // Ensure that the key vault is dropped with a majority write concern
        self::insertKeyVaultData($client, []);

        $this->clientEncryption = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => self::getAWSCredentials(),
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
        ]);
    }

    public function tearDown(): void
    {
        $this->clientEncryption = null;
        $this->database = null;
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/bc37892f360cab9df4082922384e0f4d4233f6d3/source/client-side-encryption/tests/README.rst#case-1-simple-creation-and-validation
     * @dataProvider provideKmsProviderAndMasterKey
     */
    public function testCase1_SimpleCreationAndValidation(string $kmsProvider, ?array $masterKey): void
    {
        [$result, $encryptedFields] = $this->database->createEncryptedCollection(
            $this->getCollectionName(),
            $this->clientEncryption,
            $kmsProvider,
            $masterKey,
            ['encryptedFields' => ['fields' => [['path' => 'ssn', 'bsonType' => 'string', 'keyId' => null]]]]
        );

        $this->assertCommandSucceeded($result);
        $this->assertInstanceOf(Binary::class, $encryptedFields['fields'][0]['keyId'] ?? null);

        $this->expectException(BulkWriteException::class);
        $this->expectExceptionMessage('Document failed validation');
        $this->database->selectCollection($this->getCollectionName())->insertOne(['ssn' => '123-45-6789']);
    }

    public static function provideKmsProviderAndMasterKey(): iterable
    {
        yield [
            'aws',
            ['region' => 'us-east-1', 'key' => 'arn:aws:kms:us-east-1:579766882180:key/89fcc2c4-08b0-4bd9-9f25-e30687b580d0'],
        ];

        yield [
            'local',
            null,
        ];
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/bc37892f360cab9df4082922384e0f4d4233f6d3/source/client-side-encryption/tests/README.rst#case-2-missing-encryptedfields
     * @dataProvider provideKmsProviderAndMasterKey
     */
    public function testCase2_MissingEncryptedFields(string $kmsProvider, ?array $masterKey): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"encryptedFields" option');
        $this->database->createEncryptedCollection(
            $this->getCollectionName(),
            $this->clientEncryption,
            $kmsProvider,
            $masterKey,
            []
        );
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/bc37892f360cab9df4082922384e0f4d4233f6d3/source/client-side-encryption/tests/README.rst#case-3-invalid-keyid
     * @dataProvider provideKmsProviderAndMasterKey
     */
    public function testCase3_InvalidKeyId(string $kmsProvider, ?array $masterKey): void
    {
        $this->expectException(CommandException::class);
        $this->expectExceptionCode(self::SERVER_ERROR_TYPEMISMATCH);
        $this->expectExceptionMessage('keyId');

        $this->database->createEncryptedCollection(
            $this->getCollectionName(),
            $this->clientEncryption,
            $kmsProvider,
            $masterKey,
            ['encryptedFields' => ['fields' => [['path' => 'ssn', 'bsonType' => 'string', 'keyId' => false]]]]
        );
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/bc37892f360cab9df4082922384e0f4d4233f6d3/source/client-side-encryption/tests/README.rst#case-4-insert-encrypted-value
     * @dataProvider provideKmsProviderAndMasterKey
     */
    public function testCase4_InsertEncryptedValue(string $kmsProvider, ?array $masterKey): void
    {
        [$result, $encryptedFields] = $this->database->createEncryptedCollection(
            $this->getCollectionName(),
            $this->clientEncryption,
            $kmsProvider,
            $masterKey,
            ['encryptedFields' => ['fields' => [['path' => 'ssn', 'bsonType' => 'string', 'keyId' => null]]]]
        );

        $this->assertCommandSucceeded($result);
        $this->assertInstanceOf(Binary::class, $encryptedFields['fields'][0]['keyId'] ?? null);

        $encrypted = $this->clientEncryption->encrypt('123-45-6789', [
            'keyId' => $encryptedFields['fields'][0]['keyId'],
            'algorithm' => ClientEncryption::ALGORITHM_UNINDEXED,
        ]);

        $collection = $this->database->selectCollection($this->getCollectionName());
        $insertedId = $collection->insertOne(['ssn' => $encrypted])->getInsertedId();
        $this->assertNotNull($collection->findOne(['_id' => $insertedId]));
    }
}
