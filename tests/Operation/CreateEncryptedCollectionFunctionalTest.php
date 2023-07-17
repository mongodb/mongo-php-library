<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Document;
use MongoDB\Client;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\WriteConcern;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\CreateEncryptedCollection;

use function base64_decode;
use function getenv;

class CreateEncryptedCollectionFunctionalTest extends FunctionalTestCase
{
    public const LOCAL_MASTERKEY = 'Mng0NCt4ZHVUYUJCa1kxNkVyNUR1QURhZ2h2UzR2d2RrZzh0cFBwM3R6NmdWMDFBMUN3YkQ5aXRRMkhGRGdQV09wOGVNYUMxT2k3NjZKelhaQmRCZGJkTXVyZG9uSjFk';

    private ClientEncryption $clientEncryption;

    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfClientSideEncryptionIsNotSupported();

        if (! static::isCryptSharedLibAvailable() && ! static::isMongocryptdAvailable()) {
            $this->markTestSkipped('Neither crypt_shared nor mongocryptd are available');
        }

        if ($this->isStandalone()) {
            $this->markTestSkipped('Queryable Encryption requires replica sets');
        }

        $this->skipIfServerVersion('<', '6.0.0', 'Queryable Encryption requires MongoDB 6.0 or later');

        $client = static::createTestClient();

        /* Since test cases may create encrypted collections, ensure that the
         * collection and any "enxcol_" collections do not exist. Specify
         * "encryptedFields" to ensure "enxcol_" collections are handled. */
        $client->selectCollection($this->getDatabaseName(), $this->getCollectionName())->drop(['encryptedFields' => []]);

        // Drop the key vault with a majority write concern
        $client->selectCollection('keyvault', 'datakeys')->drop(['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]);

        $this->clientEncryption = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY))],
            ],
        ]);
    }

    /** @dataProvider provideEncryptedFieldsAndFieldsIsMissing */
    public function testCreateDataKeysNopIfFieldsIsMissing($input, array $expectedOutput): void
    {
        $operation = new CreateEncryptedCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['encryptedFields' => $input],
        );

        $operation->createDataKeys(
            $this->clientEncryption,
            'local',
            null,
            $encryptedFieldsOutput,
        );

        $this->assertSame($expectedOutput, $encryptedFieldsOutput);
    }

    public function provideEncryptedFieldsAndFieldsIsMissing(): array
    {
        $ef = [];

        return [
            'array' => [$ef, $ef],
            'object' => [(object) $ef, $ef],
            'Serializable' => [new BSONDocument($ef), $ef],
            'Document' => [Document::fromPHP($ef), $ef],
        ];
    }

    /** @dataProvider provideEncryptedFieldsAndFieldsHasInvalidType */
    public function testCreateDataKeysNopIfFieldsHasInvalidType($input, array $expectedOutput): void
    {
        $operation = new CreateEncryptedCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['encryptedFields' => $input],
        );

        $operation->createDataKeys(
            $this->clientEncryption,
            'local',
            null,
            $encryptedFieldsOutput,
        );

        $this->assertSame($expectedOutput, $encryptedFieldsOutput);
    }

    public function provideEncryptedFieldsAndFieldsHasInvalidType(): array
    {
        $ef = ['fields' => 'not-an-array'];

        return [
            'array' => [$ef, $ef],
            'object' => [(object) $ef, $ef],
            'Serializable' => [new BSONDocument($ef), $ef],
            'Document' => [Document::fromPHP($ef), $ef],
        ];
    }

    /** @dataProvider provideEncryptedFieldsElementHasInvalidType */
    public function testCreateDataKeysSkipsNonDocumentFields($input, array $expectedOutput): void
    {
        $operation = new CreateEncryptedCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['encryptedFields' => $input],
        );

        $operation->createDataKeys(
            $this->clientEncryption,
            'local',
            null,
            $encryptedFieldsOutput,
        );

        $this->assertSame($expectedOutput, $encryptedFieldsOutput);
    }

    public function provideEncryptedFieldsElementHasInvalidType(): array
    {
        $ef = ['fields' => ['not-an-array-or-object']];

        return [
            'array' => [$ef, $ef],
            'object' => [(object) $ef, $ef],
            'Serializable' => [new BSONDocument(['fields' => new BSONArray(['not-an-array-or-object'])]), $ef],
            'Document' => [Document::fromPHP($ef), $ef],
        ];
    }

    public function testCreateDataKeysDoesNotModifyOriginalEncryptedFieldsOption(): void
    {
        $originalField = (object) ['path' => 'ssn', 'bsonType' => 'string', 'keyId' => null];
        $originalEncryptedFields = (object) ['fields' => [$originalField]];

        $operation = new CreateEncryptedCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['encryptedFields' => $originalEncryptedFields],
        );

        $operation->createDataKeys(
            $this->clientEncryption,
            'local',
            null,
            $modifiedEncryptedFields,
        );

        $this->assertSame($originalField, $originalEncryptedFields->fields[0]);
        $this->assertNull($originalField->keyId);

        $this->assertInstanceOf(Binary::class, $modifiedEncryptedFields['fields'][0]['keyId'] ?? null);
    }

    /** @dataProvider provideEncryptedFields */
    public function testEncryptedFieldsDocuments($input): void
    {
        $operation = new CreateEncryptedCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['encryptedFields' => $input],
        );

        $operation->createDataKeys(
            $this->clientEncryption,
            'local',
            null,
            $modifiedEncryptedFields,
        );

        $this->assertInstanceOf(Binary::class, $modifiedEncryptedFields['fields'][0]['keyId'] ?? null);
    }

    public function provideEncryptedFields(): array
    {
        $ef = ['fields' => [['path' => 'ssn', 'bsonType' => 'string', 'keyId' => null]]];

        return [
            'array' => [$ef],
            'object' => [(object) $ef],
            'Serializable' => [new BSONDocument(['fields' => new BSONArray([new BSONDocument($ef['fields'][0])])])],
            'Document' => [Document::fromPHP($ef)],
        ];
    }

    public static function createTestClient(?string $uri = null, array $options = [], array $driverOptions = []): Client
    {
        if (isset($driverOptions['autoEncryption']) && getenv('CRYPT_SHARED_LIB_PATH')) {
            $driverOptions['autoEncryption']['extraOptions']['cryptSharedLibPath'] = getenv('CRYPT_SHARED_LIB_PATH');
        }

        return parent::createTestClient($uri, $options, $driverOptions);
    }
}
