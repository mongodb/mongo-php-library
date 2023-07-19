<?php

namespace MongoDB\Tests\SpecTests\ClientSideEncryption;

use Generator;
use MongoDB\BSON\Binary;
use MongoDB\Database;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Exception\CreateEncryptedCollectionException;
use MongoDB\Exception\InvalidArgumentException;

use function base64_decode;

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

    private ?ClientEncryption $clientEncryption = null;
    private ?Database $database = null;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->isStandalone()) {
            $this->markTestSkipped('Automatic data encryption key tests require replica sets');
        }

        $this->skipIfServerVersion('<', '7.0.0', 'Automatic data encryption key tests require MongoDB 7.0 or later');

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
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY))],
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
            ['encryptedFields' => ['fields' => [['path' => 'ssn', 'bsonType' => 'string', 'keyId' => null]]]],
        );

        $this->assertCommandSucceeded($result);
        $this->assertInstanceOf(Binary::class, $encryptedFields['fields'][0]['keyId'] ?? null);

        $this->expectException(BulkWriteException::class);
        $this->expectExceptionMessage('Document failed validation');
        $this->database->selectCollection($this->getCollectionName())->insertOne(['ssn' => '123-45-6789']);
    }

    public static function provideKmsProviderAndMasterKey(): Generator
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
            [],
        );
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/bc37892f360cab9df4082922384e0f4d4233f6d3/source/client-side-encryption/tests/README.rst#case-3-invalid-keyid
     * @dataProvider provideKmsProviderAndMasterKey
     */
    public function testCase3_InvalidKeyId(string $kmsProvider, ?array $masterKey): void
    {
        try {
            $this->database->createEncryptedCollection(
                $this->getCollectionName(),
                $this->clientEncryption,
                $kmsProvider,
                $masterKey,
                ['encryptedFields' => ['fields' => [['path' => 'ssn', 'bsonType' => 'string', 'keyId' => false]]]],
            );
            $this->fail('CreateEncryptedCollectionException was not thrown');
        } catch (CreateEncryptedCollectionException $e) {
            $this->assertFalse($e->getEncryptedFields()['fields'][0]['keyId'], 'Invalid keyId should not be modified');

            $previous = $e->getPrevious();
            $this->assertInstanceOf(CommandException::class, $previous);
            $this->assertSame(self::SERVER_ERROR_TYPEMISMATCH, $previous->getCode());
            $this->assertStringContainsString('keyId', $previous->getMessage());
        }
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
            ['encryptedFields' => ['fields' => [['path' => 'ssn', 'bsonType' => 'string', 'keyId' => null]]]],
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
