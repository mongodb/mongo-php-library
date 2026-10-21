<?php

namespace MongoDB\Tests\SpecTests\ClientSideEncryption;

use Generator;
use MongoDB\BSON\Binary;
use MongoDB\BSON\Document;
use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\EncryptionException;
use MongoDB\Driver\WriteConcern;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

use function base64_decode;
use function file_get_contents;
use function in_array;
use function phpversion;
use function version_compare;

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
    private bool $serverAtLeast9;
    private WriteConcern $majorityWriteConcern;

    private static string $specDir = __DIR__ . '/../../specifications/source/client-side-encryption';

    public function setUp(): void
    {
        parent::setUp();

        if ($this->isStandalone()) {
            $this->markTestSkipped('String explicit encryption tests require replica sets');
        }

        if (version_compare(phpversion('mongodb'), '2.4.0dev', '<')) {
            $this->markTestSkipped('String explicit encryption tests require ext-mongodb 2.4.0+ (stringOpts support)');
        }

        $this->skipIfServerVersion('<', '8.2.0', 'String explicit encryption tests require MongoDB 8.2 or later');

        /* Per-case libmongocrypt version requirements (baseline 1.18.1,
         * prefix/suffix 1.19.0, prefixPreview/suffixPreview 1.19.1,
         * substring 1.20.0, substringPreview 1.18.1) are all satisfied by
         * ext-mongodb 2.4.0+, which bundles libmongocrypt 1.20.0+, so no
         * runtime libmongocrypt guard is added. */
        $this->serverAtLeast9 = version_compare($this->getServerVersion(), '9.0.0', '>=');
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
        $this->setUpCollections($database);
        $this->insertInitialDocuments($database);
    }

    public function tearDown(): void
    {
        unset($this->clientEncryption);
        unset($this->explicitEncryptedClient);
        unset($this->autoEncryptedClient);
    }

    public static function providePrefixQueryTypeAndCollection(): Generator
    {
        yield 'prefix' => ['prefix', 'prefix-suffix'];
        yield 'prefixPreview' => ['prefixPreview', 'prefix-suffix-preview'];
    }

    public static function provideSuffixQueryTypeAndCollection(): Generator
    {
        yield 'suffix' => ['suffix', 'prefix-suffix'];
        yield 'suffixPreview' => ['suffixPreview', 'prefix-suffix-preview'];
    }

    public static function provideSubstringQueryTypeAndCollection(): Generator
    {
        yield 'substring' => ['substring', 'substring'];
        yield 'substringPreview' => ['substringPreview', 'substring-preview'];
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-1-can-find-a-document-by-prefix */
    #[DataProvider('providePrefixQueryTypeAndCollection')]
    public function testCase1_CanFindADocumentByPrefix(string $queryType, string $collectionName): void
    {
        $this->skipByQueryTypeAndServerVersion($queryType);

        $encryptedFoo = $this->clientEncryption->encrypt('foo', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => $queryType,
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'prefix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection($collectionName)
            ->findOne(['$expr' => ['$encStrStartsWith' => ['input' => '$encryptedText', 'prefix' => $encryptedFoo]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['_id' => 0, 'encryptedText' => 'foobarbaz'], $result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-2-can-find-a-document-by-suffix */
    #[DataProvider('provideSuffixQueryTypeAndCollection')]
    public function testCase2_CanFindADocumentBySuffix(string $queryType, string $collectionName): void
    {
        $this->skipByQueryTypeAndServerVersion($queryType);

        $encryptedBaz = $this->clientEncryption->encrypt('baz', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => $queryType,
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'suffix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection($collectionName)
            ->findOne(['$expr' => ['$encStrEndsWith' => ['input' => '$encryptedText', 'suffix' => $encryptedBaz]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['_id' => 0, 'encryptedText' => 'foobarbaz'], $result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-3-assert-no-document-found-by-prefix */
    #[DataProvider('providePrefixQueryTypeAndCollection')]
    public function testCase3_AssertNoDocumentFoundByPrefix(string $queryType, string $collectionName): void
    {
        $this->skipByQueryTypeAndServerVersion($queryType);

        $encryptedBaz = $this->clientEncryption->encrypt('baz', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => $queryType,
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'prefix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection($collectionName)
            ->findOne(['$expr' => ['$encStrStartsWith' => ['input' => '$encryptedText', 'prefix' => $encryptedBaz]]]);

        $this->assertNull($result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-4-assert-no-document-found-by-suffix */
    #[DataProvider('provideSuffixQueryTypeAndCollection')]
    public function testCase4_AssertNoDocumentFoundBySuffix(string $queryType, string $collectionName): void
    {
        $this->skipByQueryTypeAndServerVersion($queryType);

        $encryptedFoo = $this->clientEncryption->encrypt('foo', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => $queryType,
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'suffix' => ['strMaxQueryLength' => 10, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection($collectionName)
            ->findOne(['$expr' => ['$encStrEndsWith' => ['input' => '$encryptedText', 'suffix' => $encryptedFoo]]]);

        $this->assertNull($result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-5-can-find-a-document-by-substring */
    #[DataProvider('provideSubstringQueryTypeAndCollection')]
    public function testCase5_CanFindADocumentBySubstring(string $queryType, string $collectionName): void
    {
        $this->skipByQueryTypeAndServerVersion($queryType);

        $encryptedBar = $this->clientEncryption->encrypt('bar', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => $queryType,
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'substring' => ['strMaxLength' => 10, 'strMaxQueryLength' => 6, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection($collectionName)
            ->findOne(['$expr' => ['$encStrContains' => ['input' => '$encryptedText', 'substring' => $encryptedBar]]]);

        $this->assertNotNull($result);
        $this->assertDocumentsMatch(['_id' => 0, 'encryptedText' => 'foobarbaz'], $result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-6-assert-no-document-found-by-substring */
    #[DataProvider('provideSubstringQueryTypeAndCollection')]
    public function testCase6_AssertNoDocumentFoundBySubstring(string $queryType, string $collectionName): void
    {
        $this->skipByQueryTypeAndServerVersion($queryType);

        $encryptedQux = $this->clientEncryption->encrypt('qux', [
            'keyId' => $this->key1Id,
            'algorithm' => 'String',
            'queryType' => $queryType,
            'contentionFactor' => 0,
            'stringOpts' => [
                'caseSensitive' => true,
                'diacriticSensitive' => true,
                'substring' => ['strMaxLength' => 10, 'strMaxQueryLength' => 6, 'strMinQueryLength' => 2],
            ],
        ]);

        $result = $this->explicitEncryptedClient
            ->getDatabase($this->getDatabaseName())
            ->getCollection($collectionName)
            ->findOne(['$expr' => ['$encStrContains' => ['input' => '$encryptedText', 'substring' => $encryptedQux]]]);

        $this->assertNull($result);
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#case-7-assert-contentionfactor-is-required */
    public function testCase7_AssertContentionFactorIsRequired(): void
    {
        $this->skipIfServerVersion('<', '9.0.0', 'Case 7 requires MongoDB 9.0.0+');

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
        $this->skipIfServerVersion('<', '9.0.0', 'Case 8 requires MongoDB 9.0.0+');

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
        $this->skipIfServerVersion('<', '9.0.0', 'Case 9 requires MongoDB 9.0.0+');

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
        $this->skipIfServerVersion('<', '9.0.0', 'Case 10 requires MongoDB 9.0.0+');

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
        $this->skipIfServerVersion('<', '9.0.0', 'Case 11 requires MongoDB 9.0.0+');

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

    private function setUpCollections(Database $database): void
    {
        $names = $this->serverAtLeast9
            ? ['prefix-suffix', 'prefix-suffix-ci-di', 'substring', 'substring-ci-di']
            : ['prefix-suffix-preview', 'substring-preview'];

        foreach ($names as $name) {
            $encryptedFields = Document::fromJSON(file_get_contents(self::$specDir . '/etc/data/encryptedFields-' . $name . '.json'));
            $database->dropCollection($name, ['encryptedFields' => $encryptedFields]);
            $database->createCollection($name, ['encryptedFields' => $encryptedFields]);
        }
    }

    private function insertInitialDocuments(Database $database): void
    {
        $prefixSuffixCollection = $this->serverAtLeast9 ? 'prefix-suffix' : 'prefix-suffix-preview';
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
        $database->getCollection($prefixSuffixCollection)
            ->insertOne(['_id' => 0, 'encryptedText' => $encryptedFoobarBaz]);

        $substringCollection = $this->serverAtLeast9 ? 'substring' : 'substring-preview';
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
        $database->getCollection($substringCollection)
            ->insertOne(['_id' => 0, 'encryptedText' => $encryptedFoobarBaz]);
    }

    private function skipByQueryTypeAndServerVersion(string $queryType): void
    {
        if (in_array($queryType, ['prefix', 'suffix', 'substring'], true)) {
            $this->skipIfServerVersion('<', '9.0.0', $queryType . ' query type requires MongoDB 9.0.0+');

            return;
        }

        $this->skipIfServerVersion('>=', '9.0.0', $queryType . ' query type requires MongoDB pre-9.0.0');
    }
}
