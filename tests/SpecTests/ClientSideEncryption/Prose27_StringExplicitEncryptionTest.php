<?php

namespace MongoDB\Tests\SpecTests\ClientSideEncryption;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Document;
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\EncryptionException;
use MongoDB\Driver\WriteConcern;
use PHPUnit\Framework\Attributes\Group;

use function base64_decode;
use function file_get_contents;

/**
 * Prose test 27: String Explicit Encryption
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#27-string-explicit-encryption
 */
#[Group('csfle')]
class Prose27_StringExplicitEncryptionTest extends FunctionalTestCase
{
    private ClientEncryption $clientEncryption;
    private Client $explicitEncryptedClient;
    private Client $autoEncryptedClient;
    private mixed $key1Id;
    private WriteConcern $majorityWriteConcern;

    private static string $specDir = __DIR__ . '/../../specifications/source/client-side-encryption';

    public function setUp(): void
    {
        parent::setUp();

        if ($this->isStandalone()) {
            $this->markTestSkipped('String explicit encryption tests require replica sets');
        }

        $this->skipIfServerVersion('<', '9.0.0', 'String explicit encryption tests require MongoDB 9.0 or later');

        /* Per-case libmongocrypt version requirements (baseline 1.18.1,
         * prefix/suffix 1.19.0, substring 1.20.0) are satisfied by the
         * libmongocrypt bundled with ext-mongodb 2.4+, so no runtime guard
         * is added. */
        $this->majorityWriteConcern = new WriteConcern(WriteConcern::MAJORITY);

        $client = static::createTestClient();
        $key1Document = $this->decodeJson(file_get_contents(self::$specDir . '/etc/data/keys/key1-document.json'));
        $this->key1Id = $key1Document->_id;
        self::insertKeyVaultData($client, [$key1Document]);

        $this->clientEncryption = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY))]],
        ]);

        $this->explicitEncryptedClient = self::createTestClient(null, [], [
            'autoEncryption' => [
                'keyVaultNamespace' => 'keyvault.datakeys',
                'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY))]],
                'bypassQueryAnalysis' => true,
            ],
            /* libmongocrypt caches results from listCollections. Use a new
             * client in each test to ensure its encryptedFields is applied. */
            'disableClientPersistence' => true,
        ]);

        $this->autoEncryptedClient = self::createTestClient(null, [], [
            'autoEncryption' => [
                'keyVaultNamespace' => 'keyvault.datakeys',
                'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY))]],
            ],
            'disableClientPersistence' => true,
        ]);

        $database = $this->explicitEncryptedClient->getDatabase($this->getDatabaseName(), ['writeConcern' => $this->majorityWriteConcern]);
        $this->resetCollections($database, true);
        $this->insertInitialDocuments($database);
    }

    public function tearDown(): void
    {
        try {
            if (isset($this->explicitEncryptedClient)) {
                $database = $this->explicitEncryptedClient->getDatabase($this->getDatabaseName(), ['writeConcern' => $this->majorityWriteConcern]);
                $this->resetCollections($database, false);
            }
        } finally {
            unset($this->clientEncryption);
            unset($this->explicitEncryptedClient);
            unset($this->autoEncryptedClient);
        }
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-1-can-find-a-document-by-prefix */
    public function testCase1_CanFindADocumentByPrefix(): void
    {
        $encryptedFoo = $this->clientEncryption->encrypt('foo', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'prefix',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'prefix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection('prefix-suffix')
            ->findOne(['$expr' => ['$encStrStartsWith' => ['input' => '$encryptedText', 'prefix' => $encryptedFoo]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['_id' => 0, 'encryptedText' => 'foobarbaz'], $result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-2-can-find-a-document-by-suffix */
    public function testCase2_CanFindADocumentBySuffix(): void
    {
        $encryptedBaz = $this->clientEncryption->encrypt('baz', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'suffix',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'suffix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection('prefix-suffix')
            ->findOne(['$expr' => ['$encStrEndsWith' => ['input' => '$encryptedText', 'suffix' => $encryptedBaz]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['_id' => 0, 'encryptedText' => 'foobarbaz'], $result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-3-assert-no-document-found-by-prefix */
    public function testCase3_AssertNoDocumentFoundByPrefix(): void
    {
        $encryptedBaz = $this->clientEncryption->encrypt('baz', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'prefix',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'prefix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection('prefix-suffix')
            ->findOne(['$expr' => ['$encStrStartsWith' => ['input' => '$encryptedText', 'prefix' => $encryptedBaz]]]);

        $this->assertNull($result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-4-assert-no-document-found-by-suffix */
    public function testCase4_AssertNoDocumentFoundBySuffix(): void
    {
        $encryptedFoo = $this->clientEncryption->encrypt('foo', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'suffix',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'suffix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection('prefix-suffix')
            ->findOne(['$expr' => ['$encStrEndsWith' => ['input' => '$encryptedText', 'suffix' => $encryptedFoo]]]);

        $this->assertNull($result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-5-can-find-a-document-by-substring */
    public function testCase5_CanFindADocumentBySubstring(): void
    {
        $encryptedBar = $this->clientEncryption->encrypt('bar', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'substring',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'substring' => ['strMaxLength' => 10, 'strMaxQueryLength' => 6, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection('substring')
            ->findOne(['$expr' => ['$encStrContains' => ['input' => '$encryptedText', 'substring' => $encryptedBar]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['_id' => 0, 'encryptedText' => 'foobarbaz'], $result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-6-assert-no-document-found-by-substring */
    public function testCase6_AssertNoDocumentFoundBySubstring(): void
    {
        $encryptedQux = $this->clientEncryption->encrypt('qux', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'substring',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'substring' => ['strMaxLength' => 10, 'strMaxQueryLength' => 6, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection('substring')
            ->findOne(['$expr' => ['$encStrContains' => ['input' => '$encryptedText', 'substring' => $encryptedQux]]]);

        $this->assertNull($result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-7-assert-contentionfactor-is-required */
    public function testCase7_AssertContentionFactorIsRequired(): void
    {
        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessageMatches('/contention factor is required for string algorithm/');

        $this->clientEncryption->encrypt('foo', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'prefix',
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'prefix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-8-can-find-an-auto-encrypted-case-insensitively-indexed-document-by-prefix-and-suffix */
    public function testCase8_CanFindAutoEncryptedCaseInsensitiveDocumentByPrefixAndSuffix(): void
    {
        $database = $this->getDatabaseName();

        $this->autoEncryptedClient
            ->getDatabase($database)
            ->getCollection('prefix-suffix-ci-di', ['writeConcern' => $this->majorityWriteConcern])
            ->insertOne(['encryptedText' => 'BingQiLin']);

        $encryptedBing = $this->clientEncryption->encrypt('bing', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'prefix',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => false,
                'diacriticSensitive' => false,
                'prefix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($database)
            ->getCollection('prefix-suffix-ci-di')
            ->findOne(['$expr' => ['$encStrStartsWith' => ['input' => '$encryptedText', 'prefix' => $encryptedBing]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['encryptedText' => 'BingQiLin'], $result);

        $encryptedLin = $this->clientEncryption->encrypt('lin', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'suffix',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => false,
                'diacriticSensitive' => false,
                'suffix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($database)
            ->getCollection('prefix-suffix-ci-di')
            ->findOne(['$expr' => ['$encStrEndsWith' => ['input' => '$encryptedText', 'suffix' => $encryptedLin]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['encryptedText' => 'BingQiLin'], $result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-9-can-find-an-auto-encrypted-diacritic-insensitively-indexed-document-by-prefix-and-suffix */
    public function testCase9_CanFindAutoEncryptedDiacriticInsensitiveDocumentByPrefixAndSuffix(): void
    {
        $database = $this->getDatabaseName();

        $this->autoEncryptedClient
            ->getDatabase($database)
            ->getCollection('prefix-suffix-ci-di', ['writeConcern' => $this->majorityWriteConcern])
            ->insertOne(['encryptedText' => 'cafébarbäz']);

        $encryptedCafe = $this->clientEncryption->encrypt('cafe', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'prefix',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => false,
                'diacriticSensitive' => false,
                'prefix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($database)
            ->getCollection('prefix-suffix-ci-di')
            ->findOne(['$expr' => ['$encStrStartsWith' => ['input' => '$encryptedText', 'prefix' => $encryptedCafe]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['encryptedText' => 'cafébarbäz'], $result);

        $encryptedBaz = $this->clientEncryption->encrypt('baz', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'suffix',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => false,
                'diacriticSensitive' => false,
                'suffix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($database)
            ->getCollection('prefix-suffix-ci-di')
            ->findOne(['$expr' => ['$encStrEndsWith' => ['input' => '$encryptedText', 'suffix' => $encryptedBaz]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['encryptedText' => 'cafébarbäz'], $result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-10-can-find-an-auto-encrypted-case-insensitively-indexed-document-by-substring */
    public function testCase10_CanFindAutoEncryptedCaseInsensitiveDocumentBySubstring(): void
    {
        $database = $this->getDatabaseName();

        $this->autoEncryptedClient
            ->getDatabase($database)
            ->getCollection('substring-ci-di', ['writeConcern' => $this->majorityWriteConcern])
            ->insertOne(['encryptedText' => 'FooBarBaz']);

        $encryptedBar = $this->clientEncryption->encrypt('bar', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'substring',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => false,
                'diacriticSensitive' => false,
                'substring' => ['strMaxLength' => 10, 'strMaxQueryLength' => 6, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($database)
            ->getCollection('substring-ci-di')
            ->findOne(['$expr' => ['$encStrContains' => ['input' => '$encryptedText', 'substring' => $encryptedBar]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['encryptedText' => 'FooBarBaz'], $result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-11-can-find-an-auto-encrypted-diacritic-insensitively-indexed-document-by-substring */
    public function testCase11_CanFindAutoEncryptedDiacriticInsensitiveDocumentBySubstring(): void
    {
        $database = $this->getDatabaseName();

        $this->autoEncryptedClient
            ->getDatabase($database)
            ->getCollection('substring-ci-di', ['writeConcern' => $this->majorityWriteConcern])
            ->insertOne(['encryptedText' => 'foocafébaz']);

        $encryptedCafe = $this->clientEncryption->encrypt('cafe', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => 'substring',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => false,
                'diacriticSensitive' => false,
                'substring' => ['strMaxLength' => 10, 'strMaxQueryLength' => 6, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($database)
            ->getCollection('substring-ci-di')
            ->findOne(['$expr' => ['$encStrContains' => ['input' => '$encryptedText', 'substring' => $encryptedCafe]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['encryptedText' => 'foocafébaz'], $result);
    }

    private function resetCollections(Database $database, bool $create): void
    {
        $names = ['prefix-suffix', 'prefix-suffix-ci-di', 'substring', 'substring-ci-di'];

        foreach ($names as $name) {
            $encryptedFields = Document::fromJSON(file_get_contents(self::$specDir . '/etc/data/encryptedFields-' . $name . '.json'));
            $database->dropCollection($name, ['encryptedFields' => $encryptedFields]);

            if ($create) {
                $database->createCollection($name, ['encryptedFields' => $encryptedFields]);
            }
        }
    }

    private function insertInitialDocuments(Database $database): void
    {
        $encryptedFoobarBaz = $this->clientEncryption->encrypt('foobarbaz', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'prefix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
                'suffix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);
        $database->getCollection('prefix-suffix')
            ->insertOne(['_id' => 0, 'encryptedText' => $encryptedFoobarBaz]);

        $encryptedFoobarBaz = $this->clientEncryption->encrypt('foobarbaz', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'substring' => ['strMaxLength' => 10, 'strMaxQueryLength' => 6, 'strMinQueryLength' => 2],
            ],
        ]);
        $database->getCollection('substring')
            ->insertOne(['_id' => 0, 'encryptedText' => $encryptedFoobarBaz]);
    }
}
