<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Binary;
use MongoDB\Client;
use MongoDB\ClientEncryption;
use MongoDB\Driver\WriteConcern;
use MongoDB\Operation\CreateEncryptedCollection;

use function base64_decode;
use function explode;
use function getenv;
use function is_executable;
use function is_readable;
use function version_compare;

use const DIRECTORY_SEPARATOR;
use const PATH_SEPARATOR;

class CreateEncryptedCollectionFunctionalTest extends FunctionalTestCase
{
    public const LOCAL_MASTERKEY = 'Mng0NCt4ZHVUYUJCa1kxNkVyNUR1QURhZ2h2UzR2d2RrZzh0cFBwM3R6NmdWMDFBMUN3YkQ5aXRRMkhGRGdQV09wOGVNYUMxT2k3NjZKelhaQmRCZGJkTXVyZG9uSjFk';

    /** @var ClientEncryption */
    private $clientEncryption;

    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfClientSideEncryptionIsNotSupported();

        if (! static::isCryptSharedLibAvailable() && ! static::isMongocryptdAvailable()) {
            $this->markTestSkipped('Neither crypt_shared nor mongocryptd are available');
        }

        if (version_compare($this->getServerVersion(), '6.0.0', '<')) {
            $this->markTestSkipped('Queryable Encryption requires MongoDB 6.0 or later');
        }

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
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
        ]);
    }

    public function testCreateDataKeysNopIfFieldsArrayIsMissing(): void
    {
        $operation = new CreateEncryptedCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['encryptedFields' => []]
        );

        $operation->createDataKeys(
            $this->clientEncryption,
            'local',
            null,
            $encryptedFields
        );

        $this->assertSame([], $encryptedFields);
    }

    public function testCreateDataKeysNopIfFieldsArrayIsInvalid(): void
    {
        $operation = new CreateEncryptedCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['encryptedFields' => ['fields' => 'not-an-array']]
        );

        $operation->createDataKeys(
            $this->clientEncryption,
            'local',
            null,
            $encryptedFields
        );

        $this->assertSame(['fields' => 'not-an-array'], $encryptedFields);
    }

    public function testCreateDataKeysSkipsNonDocumentFields(): void
    {
        $operation = new CreateEncryptedCollection(
            $this->getDatabaseName(),
            $this->getCollectionName(),
            ['encryptedFields' => ['fields' => ['not-an-array-or-object']]]
        );

        $operation->createDataKeys(
            $this->clientEncryption,
            'local',
            null,
            $encryptedFields
        );

        $this->assertSame(['fields' => ['not-an-array-or-object']], $encryptedFields);
    }

    public static function createTestClient(?string $uri = null, array $options = [], array $driverOptions = []): Client
    {
        if (isset($driverOptions['autoEncryption']) && getenv('CRYPT_SHARED_LIB_PATH')) {
            $driverOptions['autoEncryption']['extraOptions']['cryptSharedLibPath'] = getenv('CRYPT_SHARED_LIB_PATH');
        }

        return parent::createTestClient($uri, $options, $driverOptions);
    }

    private static function isCryptSharedLibAvailable(): bool
    {
        $cryptSharedLibPath = getenv('CRYPT_SHARED_LIB_PATH');

        if ($cryptSharedLibPath === false) {
            return false;
        }

        return is_readable($cryptSharedLibPath);
    }

    private static function isMongocryptdAvailable(): bool
    {
        $paths = explode(PATH_SEPARATOR, getenv("PATH"));

        foreach ($paths as $path) {
            if (is_executable($path . DIRECTORY_SEPARATOR . 'mongocryptd')) {
                return true;
            }
        }

        return false;
    }
}
