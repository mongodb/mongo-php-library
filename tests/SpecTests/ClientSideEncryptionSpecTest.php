<?php

namespace MongoDB\Tests\SpecTests;

use Closure;
use MongoDB\BSON\Binary;
use MongoDB\BSON\Int64;
use MongoDB\Collection;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\AuthenticationException;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use MongoDB\Driver\Exception\EncryptionException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\WriteConcern;
use MongoDB\Operation\CreateCollection;
use MongoDB\Tests\CommandObserver;
use PHPUnit\Framework\SkippedTestError;
use stdClass;
use Throwable;
use UnexpectedValueException;

use function base64_decode;
use function basename;
use function file_get_contents;
use function glob;
use function in_array;
use function iterator_to_array;
use function json_decode;
use function sprintf;
use function str_repeat;
use function strlen;
use function unserialize;

/**
 * Client-side encryption spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption
 */
class ClientSideEncryptionSpecTest extends FunctionalTestCase
{
    public const LOCAL_MASTERKEY = 'Mng0NCt4ZHVUYUJCa1kxNkVyNUR1QURhZ2h2UzR2d2RrZzh0cFBwM3R6NmdWMDFBMUN3YkQ5aXRRMkhGRGdQV09wOGVNYUMxT2k3NjZKelhaQmRCZGJkTXVyZG9uSjFk';

    /** @var array */
    private static $incompleteTests = [
        'awsTemporary: Insert a document with auto encryption using the AWS provider with temporary credentials' => 'Not yet implemented (PHPC-1751)',
        'awsTemporary: Insert with invalid temporary credentials' => 'Not yet implemented (PHPC-1751)',
        'azureKMS: Insert a document with auto encryption using Azure KMS provider' => 'RHEL platform is missing Azure root certificate (PHPLIB-619)',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfClientSideEncryptionIsNotSupported();
    }

    /**
     * Assert that the expected and actual command documents match.
     *
     * @param stdClass $expected Expected command document
     * @param stdClass $actual   Actual command document
     */
    public static function assertCommandMatches(stdClass $expected, stdClass $actual): void
    {
        static::assertDocumentsMatch($expected, $actual);
    }

    /**
     * Execute an individual test case from the specification.
     *
     * @dataProvider provideTests
     * @param stdClass    $test           Individual "tests[]" document
     * @param array       $runOn          Top-level "runOn" array with server requirements
     * @param array       $data           Top-level "data" array to initialize collection
     * @param array|null  $keyVaultData   Top-level "key_vault_data" array to initialize keyvault.datakeys collection
     * @param object|null $jsonSchema     Top-level "json_schema" array to initialize collection
     * @param string      $databaseName   Name of database under test
     * @param string      $collectionName Name of collection under test
     */
    public function testClientSideEncryption(stdClass $test, ?array $runOn = null, array $data, ?array $keyVaultData = null, $jsonSchema = null, ?string $databaseName = null, ?string $collectionName = null): void
    {
        if (isset(self::$incompleteTests[$this->dataDescription()])) {
            $this->markTestIncomplete(self::$incompleteTests[$this->dataDescription()]);
        }

        if (isset($runOn)) {
            $this->checkServerRequirements($runOn);
        }

        if (isset($test->skipReason)) {
            $this->markTestSkipped($test->skipReason);
        }

        $databaseName = $databaseName ?? $this->getDatabaseName();
        $collectionName = $collectionName ?? $this->getCollectionName();

        try {
            $context = Context::fromClientSideEncryption($test, $databaseName, $collectionName);
        } catch (SkippedTestError $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->setContext($context);

        $this->insertKeyVaultData($keyVaultData);
        $this->dropTestAndOutcomeCollections();
        $this->createTestCollection($jsonSchema);
        $this->insertDataFixtures($data);

        if (isset($test->failPoint)) {
            $this->configureFailPoint($test->failPoint);
        }

        $context->enableEncryption();

        if (isset($test->expectations)) {
            $commandExpectations = CommandExpectations::fromClientSideEncryption($test->expectations);
            $commandExpectations->startMonitoring();
        }

        foreach ($test->operations as $operation) {
            Operation::fromClientSideEncryption($operation)->assert($this, $context);
        }

        if (isset($commandExpectations)) {
            $commandExpectations->stopMonitoring();
            $commandExpectations->assert($this, $context);
        }

        $context->disableEncryption();

        if (isset($test->outcome->collection->data)) {
            $this->assertOutcomeCollectionData($test->outcome->collection->data, ResultExpectation::ASSERT_DOCUMENTS_MATCH);
        }
    }

    public function provideTests()
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/client-side-encryption/tests/*.json') as $filename) {
            $group = basename($filename, '.json');

            try {
                $json = $this->decodeJson(file_get_contents($filename));
            } catch (Throwable $e) {
                $testArgs[$group] = [
                    (object) ['skipReason' => sprintf('Exception loading file "%s": %s', $filename, $e->getMessage())],
                    null,
                    [],
                ];

                continue;
            }

            $runOn = $json->runOn ?? null;
            $data = $json->data ?? [];
            // phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
            $keyVaultData = $json->key_vault_data ?? null;
            $jsonSchema = $json->json_schema ?? null;
            $databaseName = $json->database_name ?? null;
            $collectionName = $json->collection_name ?? null;
            // phpcs:enable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[$name] = [$test, $runOn, $data, $keyVaultData, $jsonSchema, $databaseName, $collectionName];
            }
        }

        return $testArgs;
    }

    /**
     * Prose test: Data key and double encryption
     *
     * @dataProvider dataKeyProvider
     */
    public function testDataKeyAndDoubleEncryption(string $providerName, $masterKey): void
    {
        $this->setContext(Context::fromClientSideEncryption((object) [], 'db', 'coll'));
        $client = $this->getContext()->getClient();

        // This empty call ensures that the key vault is dropped with a majority
        // write concern
        $this->insertKeyVaultData([]);
        $client->selectCollection('db', 'coll')->drop();

        $encryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
                'azure' => Context::getAzureCredentials(),
                'gcp' => Context::getGCPCredentials(),
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
        ];

        $autoEncryptionOpts = $encryptionOpts + [
            'schemaMap' => [
                'db.coll' => [
                    'bsonType' => 'object',
                    'properties' => [
                        'encrypted_placeholder' => [
                            'encrypt' => [
                                'keyId' => '/placeholder',
                                'bsonType' => 'string',
                                'algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_RANDOM,
                            ],
                        ],
                    ],
                ],
            ],
            'keyVaultClient' => $client,
        ];

        $clientEncrypted = static::createTestClient(null, [], ['autoEncryption' => $autoEncryptionOpts]);
        $clientEncryption = $clientEncrypted->createClientEncryption($encryptionOpts);

        $commands = [];

        $dataKeyId = null;
        $keyAltName = $providerName . '_altname';

        (new CommandObserver())->observe(
            function () use ($clientEncryption, &$dataKeyId, $keyAltName, $providerName, $masterKey): void {
                $keyData = ['keyAltNames' => [$keyAltName]];
                if ($masterKey !== null) {
                    $keyData['masterKey'] = $masterKey;
                }

                $dataKeyId = $clientEncryption->createDataKey($providerName, $keyData);
            },
            function ($command) use (&$commands): void {
                $commands[] = $command;
            }
        );

        $this->assertInstanceOf(Binary::class, $dataKeyId);
        $this->assertSame(Binary::TYPE_UUID, $dataKeyId->getType());

        $this->assertCount(2, $commands);
        $insert = $commands[1]['started'];
        $this->assertSame('insert', $insert->getCommandName());
        $this->assertSame(WriteConcern::MAJORITY, $insert->getCommand()->writeConcern->w);

        $keys = $client->selectCollection('keyvault', 'datakeys')->find(['_id' => $dataKeyId]);
        $keys = iterator_to_array($keys);
        $this->assertCount(1, $keys);

        $key = $keys[0];
        $this->assertNotNull($key);
        $this->assertSame($providerName, $key['masterKey']['provider']);

        $encrypted = $clientEncryption->encrypt('hello ' . $providerName, ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $dataKeyId]);
        $this->assertInstanceOf(Binary::class, $encrypted);
        $this->assertSame(Binary::TYPE_ENCRYPTED, $encrypted->getType());

        $clientEncrypted->selectCollection('db', 'coll')->insertOne(['_id' => 'local', 'value' => $encrypted]);
        $hello = $clientEncrypted->selectCollection('db', 'coll')->findOne(['_id' => 'local']);
        $this->assertNotNull($hello);
        $this->assertSame('hello ' . $providerName, $hello['value']);

        $encryptedAltName = $clientEncryption->encrypt('hello ' . $providerName, ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyAltName' => $keyAltName]);
        $this->assertEquals($encrypted, $encryptedAltName);

        $this->expectException(BulkWriteException::class);
        $clientEncrypted->selectCollection('db', 'coll')->insertOne(['encrypted_placeholder' => $encrypted]);
    }

    public static function dataKeyProvider()
    {
        return [
            'local' => [
                'providerName' => 'local',
                'masterKey' => null,
            ],
            'aws' => [
                'providerName' => 'aws',
                'masterKey' => [
                    'region' => 'us-east-1',
                    'key' => 'arn:aws:kms:us-east-1:579766882180:key/89fcc2c4-08b0-4bd9-9f25-e30687b580d0',
                ],
            ],
            'azure' => [
                'providerName' => 'azure',
                'masterKey' => [
                    'keyVaultEndpoint' => 'key-vault-csfle.vault.azure.net',
                    'keyName' => 'key-name-csfle',
                ],
            ],
            'gcp' => [
                'providerName' => 'gcp',
                'masterKey' => [
                    'projectId' => 'devprod-drivers',
                    'location' => 'global',
                    'keyRing' => 'key-ring-csfle',
                    'keyName' => 'key-name-csfle',
                ],
            ],
        ];
    }

    /**
     * Prose test: External Key Vault
     *
     * @testWith [false]
     *           [true]
     */
    public function testExternalKeyVault($withExternalKeyVault): void
    {
        $this->setContext(Context::fromClientSideEncryption((object) [], 'db', 'coll'));
        $client = $this->getContext()->getClient();
        $client->selectCollection('db', 'coll')->drop();

        $keyVaultCollection = $client->selectCollection(
            'keyvault',
            'datakeys',
            ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)] + $this->getContext()->defaultWriteOptions
        );
        $keyVaultCollection->drop();
        $keyId = $keyVaultCollection
            ->insertOne($this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/external/external-key.json')))
            ->getInsertedId();

        $encryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
        ];

        if ($withExternalKeyVault) {
            $encryptionOpts['keyVaultClient'] = static::createTestClient(null, ['username' => 'fake-user', 'password' => 'fake-pwd']);
        }

        $autoEncryptionOpts = $encryptionOpts + [
            'schemaMap' => [
                'db.coll' => $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/external/external-schema.json')),
            ],
        ];

        $clientEncrypted = static::createTestClient(null, [], ['autoEncryption' => $autoEncryptionOpts]);
        $clientEncryption = $clientEncrypted->createClientEncryption($encryptionOpts);

        try {
            $result = $clientEncrypted->selectCollection('db', 'coll')->insertOne(['encrypted' => 'test']);

            if ($withExternalKeyVault) {
                $this->fail('Expected exception to be thrown');
            } else {
                $this->assertSame(1, $result->getInsertedCount());
            }
        } catch (BulkWriteException $e) {
            if (! $withExternalKeyVault) {
                throw $e;
            }

            $this->assertInstanceOf(AuthenticationException::class, $e->getPrevious());
        }

        if ($withExternalKeyVault) {
            $this->expectException(AuthenticationException::class);
        }

        $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
    }

    public static function provideBSONSizeLimitsAndBatchSplittingTests()
    {
        yield 'Test 1' => [
            static function (self $test, Collection $collection): void {
                $collection->insertOne(['_id' => 'over_2mib_under_16mib', 'unencrypted' => str_repeat('a', 2097152)]);
                $test->assertCollectionCount($collection->getNamespace(), 1);
            },
        ];

        yield 'Test 2' => [
            static function (self $test, Collection $collection, array $document): void {
                $collection->insertOne(
                    ['_id' => 'encryption_exceeds_2mib', 'unencrypted' => str_repeat('a', 2097152 - 2000)] + $document
                );
                $test->assertCollectionCount($collection->getNamespace(), 1);
            },
        ];

        yield 'Test 3' => [
            static function (self $test, Collection $collection): void {
                $commands = [];
                (new CommandObserver())->observe(
                    function () use ($collection): void {
                        $collection->insertMany([
                            ['_id' => 'over_2mib_1', 'unencrypted' => str_repeat('a', 2097152)],
                            ['_id' => 'over_2mib_2', 'unencrypted' => str_repeat('a', 2097152)],
                        ]);
                    },
                    function ($command) use (&$commands): void {
                        if ($command['started']->getCommandName() !== 'insert') {
                            return;
                        }

                        $commands[] = $command;
                    }
                );

                $test->assertCount(2, $commands);
            },
        ];

        yield 'Test 4' => [
            static function (self $test, Collection $collection, array $document): void {
                $commands = [];
                (new CommandObserver())->observe(
                    function () use ($collection, $document): void {
                        $collection->insertMany([
                            [
                                '_id' => 'encryption_exceeds_2mib_1',
                                'unencrypted' => str_repeat('a', 2097152 - 2000),
                            ] + $document,
                            [
                                '_id' => 'encryption_exceeds_2mib_2',
                                'unencrypted' => str_repeat('a', 2097152 - 2000),
                            ] + $document,
                        ]);
                    },
                    function ($command) use (&$commands): void {
                        if ($command['started']->getCommandName() !== 'insert') {
                            return;
                        }

                        $commands[] = $command;
                    }
                );

                $test->assertCount(2, $commands);
            },
        ];

        yield 'Test 5' => [
            static function (self $test, Collection $collection): void {
                $collection->insertOne(['_id' => 'under_16mib', 'unencrypted' => str_repeat('a', 16777216 - 2000)]);
                $test->assertCollectionCount($collection->getNamespace(), 1);
            },
        ];

        yield 'Test 6' => [
            static function (self $test, Collection $collection, array $document): void {
                $test->expectException(BulkWriteException::class);
                $test->expectExceptionMessageMatches('#object to insert too large#');
                $collection->insertOne(['_id' => 'encryption_exceeds_16mib', 'unencrypted' => str_repeat('a', 16777216 - 2000)] + $document);
            },
        ];
    }

    /**
     * Prose test: BSON size limits and batch splitting
     *
     * @dataProvider provideBSONSizeLimitsAndBatchSplittingTests
     */
    public function testBSONSizeLimitsAndBatchSplitting(Closure $test): void
    {
        $this->setContext(Context::fromClientSideEncryption((object) [], 'db', 'coll'));
        $client = $this->getContext()->getClient();

        $client->selectCollection('db', 'coll')->drop();
        $client->selectDatabase('db')->createCollection('coll', ['validator' => ['$jsonSchema' => $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/limits/limits-schema.json'))]]);

        $this->insertKeyVaultData([
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/limits/limits-key.json')),
        ]);

        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
            'keyVaultClient' => $client,
        ];

        $clientEncrypted = static::createTestClient(null, [], ['autoEncryption' => $autoEncryptionOpts]);

        $collection = $clientEncrypted->selectCollection('db', 'coll');

        $document = json_decode(file_get_contents(__DIR__ . '/client-side-encryption/limits/limits-doc.json'), true);

        $test($this, $collection, $document);
    }

    /**
     * Prose test: Views are prohibited
     */
    public function testViewsAreProhibited(): void
    {
        $client = static::createTestClient();

        $client->selectCollection('db', 'view')->drop();
        $client->selectDatabase('db')->command(['create' => 'view', 'viewOn' => 'coll']);

        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
        ];

        $clientEncrypted = static::createTestClient(null, [], ['autoEncryption' => $autoEncryptionOpts]);

        try {
            $clientEncrypted->selectCollection('db', 'view')->insertOne(['foo' => 'bar']);
            $this->fail('Expected exception to be thrown');
        } catch (BulkWriteException $e) {
            $previous = $e->getPrevious();

            $this->assertInstanceOf(EncryptionException::class, $previous);
            $this->assertSame('cannot auto encrypt a view', $previous->getMessage());
        }
    }

    /**
     * Prose test: BSON Corpus
     *
     * @testWith [true]
     *           [false]
     */
    public function testCorpus($schemaMap = true): void
    {
        $this->setContext(Context::fromClientSideEncryption((object) [], 'db', 'coll'));
        $client = $this->getContext()->getClient();

        $client->selectDatabase('db')->dropCollection('coll');

        $schema = $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-schema.json'));

        if (! $schemaMap) {
            $client
                ->selectDatabase('db')
                ->createCollection('coll', ['validator' => ['$jsonSchema' => $schema]]);
        }

        $this->insertKeyVaultData([
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-local.json')),
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-aws.json')),
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-azure.json')),
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-gcp.json')),
        ]);

        $encryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
                'azure' => Context::getAzureCredentials(),
                'gcp' => Context::getGCPCredentials(),
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
        ];

        $autoEncryptionOpts = $encryptionOpts;

        if ($schemaMap) {
            $autoEncryptionOpts += [
                'schemaMap' => ['db.coll' => $schema],
            ];
        }

        $corpus = (array) $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus.json'));
        $corpusCopied = [];

        $clientEncrypted = static::createTestClient(null, [], ['autoEncryption' => $autoEncryptionOpts]);
        $clientEncryption = $clientEncrypted->createClientEncryption($encryptionOpts);

        $collection = $clientEncrypted->selectCollection('db', 'coll');

        $unpreparedFieldNames = [
            '_id',
            'altname_aws',
            'altname_azure',
            'altname_gcp',
            'altname_local',
        ];

        foreach ($corpus as $fieldName => $data) {
            if (in_array($fieldName, $unpreparedFieldNames, true)) {
                $corpusCopied[$fieldName] = $data;
                continue;
            }

            $corpusCopied[$fieldName] = $this->prepareCorpusData($fieldName, $data, $clientEncryption);
        }

        $collection->insertOne($corpusCopied);
        $corpusDecrypted = $collection->findOne(['_id' => 'client_side_encryption_corpus']);

        $this->assertDocumentsMatch($corpus, $corpusDecrypted);

        $corpusEncryptedExpected = (array) $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-encrypted.json'));
        $corpusEncryptedActual = $client->selectCollection('db', 'coll')->findOne(['_id' => 'client_side_encryption_corpus'], ['typeMap' => ['root' => 'array', 'document' => stdClass::class, 'array' => 'array']]);

        foreach ($corpusEncryptedExpected as $fieldName => $expectedData) {
            if (in_array($fieldName, $unpreparedFieldNames, true)) {
                continue;
            }

            $actualData = $corpusEncryptedActual[$fieldName];

            if ($expectedData->algo === 'det') {
                $this->assertEquals($expectedData->value, $actualData->value, 'Value for field ' . $fieldName . ' does not match expected value.');
            }

            if ($expectedData->allowed) {
                if ($expectedData->algo === 'rand') {
                    $this->assertNotEquals($expectedData->value, $actualData->value, 'Value for field ' . $fieldName . ' does not differ from expected value.');
                }

                $this->assertEquals(
                    $clientEncryption->decrypt($expectedData->value),
                    $clientEncryption->decrypt($actualData->value),
                    'Decrypted value for field ' . $fieldName . ' does not match.'
                );
            } else {
                $this->assertEquals($corpus[$fieldName]->value, $actualData->value, 'Value for field ' . $fieldName . ' does not match original value.');
            }
        }
    }

    /**
     * Prose test: Custom Endpoint
     *
     * @dataProvider customEndpointProvider
     */
    public function testCustomEndpoint(Closure $test): void
    {
        $client = static::createTestClient();

        $clientEncryption = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
                'azure' => Context::getAzureCredentials() + ['identityPlatformEndpoint' => 'login.microsoftonline.com:443'],
                'gcp' => Context::getGCPCredentials() + ['endpoint' => 'oauth2.googleapis.com:443'],
            ],
        ]);

        $clientEncryptionInvalid = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'azure' => Context::getAzureCredentials() + ['identityPlatformEndpoint' => 'example.com:443'],
                'gcp' => Context::getGCPCredentials() + ['endpoint' => 'example.com:443'],
            ],
        ]);

        $test($this, $clientEncryption, $clientEncryptionInvalid);
    }

    public static function customEndpointProvider()
    {
        $awsMasterKey = ['region' => 'us-east-1', 'key' => 'arn:aws:kms:us-east-1:579766882180:key/89fcc2c4-08b0-4bd9-9f25-e30687b580d0'];
        $azureMasterKey = ['keyVaultEndpoint' => 'key-vault-csfle.vault.azure.net', 'keyName' => 'key-name-csfle'];
        $gcpMasterKey = [
            'projectId' => 'devprod-drivers',
            'location' => 'global',
            'keyRing' => 'key-ring-csfle',
            'keyName' => 'key-name-csfle',
            'endpoint' => 'cloudkms.googleapis.com:443',
        ];

        yield 'Test 1' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($awsMasterKey): void {
                $keyId = $clientEncryption->createDataKey('aws', ['masterKey' => $awsMasterKey]);
                $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
                $test->assertSame('test', $clientEncryption->decrypt($encrypted));
            },
        ];

        yield 'Test 2' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($awsMasterKey): void {
                $keyId = $clientEncryption->createDataKey('aws', ['masterKey' => $awsMasterKey + ['endpoint' => 'kms.us-east-1.amazonaws.com']]);
                $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
                $test->assertSame('test', $clientEncryption->decrypt($encrypted));
            },
        ];

        yield 'Test 3' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($awsMasterKey): void {
                $keyId = $clientEncryption->createDataKey('aws', ['masterKey' => $awsMasterKey + [ 'endpoint' => 'kms.us-east-1.amazonaws.com:443']]);
                $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
                $test->assertSame('test', $clientEncryption->decrypt($encrypted));
            },
        ];

        yield 'Test 4' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($awsMasterKey): void {
                $test->expectException(ConnectionException::class);
                $clientEncryption->createDataKey('aws', ['masterKey' => $awsMasterKey + ['endpoint' => 'kms.us-east-1.amazonaws.com:12345']]);
            },
        ];

        yield 'Test 5' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($awsMasterKey): void {
                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#us-east-1#');
                $clientEncryption->createDataKey('aws', ['masterKey' => $awsMasterKey + ['endpoint' => 'kms.us-east-2.amazonaws.com']]);
            },
        ];

        yield 'Test 6' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($awsMasterKey): void {
                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#parse error#');
                $clientEncryption->createDataKey('aws', ['masterKey' => $awsMasterKey + ['endpoint' => 'example.com']]);
            },
        ];

        yield 'Test 7' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($azureMasterKey): void {
                $keyId = $clientEncryption->createDataKey('azure', ['masterKey' => $azureMasterKey]);
                $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
                $test->assertSame('test', $clientEncryption->decrypt($encrypted));

                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#parse error#');
                $clientEncryptionInvalid->createDataKey('azure', ['masterKey' => $azureMasterKey]);
            },
        ];

        yield 'Test 8' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($gcpMasterKey): void {
                $keyId = $clientEncryption->createDataKey('gcp', ['masterKey' => $gcpMasterKey]);
                $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
                $test->assertSame('test', $clientEncryption->decrypt($encrypted));

                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#parse error#');
                $clientEncryptionInvalid->createDataKey('gcp', ['masterKey' => $gcpMasterKey]);
            },
        ];

        yield 'Test 9' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($gcpMasterKey): void {
                $masterKey = $gcpMasterKey;
                $masterKey['endpoint'] = 'example.com:443';

                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#Invalid KMS response#');
                $clientEncryption->createDataKey('gcp', ['masterKey' => $masterKey]);
            },
        ];
    }

    /**
     * Prose test: Bypass spawning mongocryptd (via mongocryptdBypassSpawn)
     */
    public function testBypassSpawningMongocryptdViaBypassSpawn(): void
    {
        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
            'schemaMap' => [
                'db.coll' => $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/external/external-schema.json')),
            ],
            'extraOptions' => [
                'mongocryptdBypassSpawn' => true,
                'mongocryptdURI' => 'mongodb://localhost:27021/db?serverSelectionTimeoutMS=1000',
                'mongocryptdSpawnArgs' => ['--pidfilepath=bypass-spawning-mongocryptd.pid', '--port=27021'],
            ],
        ];

        $clientEncrypted = static::createTestClient(null, [], ['autoEncryption' => $autoEncryptionOpts]);

        try {
            $clientEncrypted->selectCollection('db', 'coll')->insertOne(['encrypted' => 'test']);
            $this->fail('Expected exception to be thrown');
        } catch (BulkWriteException $e) {
            $previous = $e->getPrevious();
            $this->assertInstanceOf(ConnectionTimeoutException::class, $previous);

            $this->assertStringContainsString('mongocryptd error: No suitable servers found', $previous->getMessage());
        }
    }

    /**
     * Bypass spawning mongocryptd (via bypassAutoEncryption)
     */
    public function testBypassSpawningMongocryptdViaBypassAutoEncryption(): void
    {
        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
            'bypassAutoEncryption' => true,
            'extraOptions' => [
                'mongocryptdSpawnArgs' => ['--pidfilepath=bypass-spawning-mongocryptd.pid', '--port=27021'],
            ],
        ];

        $clientEncrypted = static::createTestClient(null, [], ['autoEncryption' => $autoEncryptionOpts]);

        $clientEncrypted->selectCollection('db', 'coll')->insertOne(['encrypted' => 'test']);

        $clientMongocryptd = static::createTestClient('mongodb://localhost:27021');

        $this->expectException(ConnectionTimeoutException::class);
        $clientMongocryptd->selectDatabase('db')->command(['ping' => 1]);
    }

    /**
     * Casts the value for a BSON corpus structure to int64 if necessary.
     *
     * This is a workaround for an issue in mongocryptd which refuses to encrypt
     * int32 values if the schemaMap defines a "long" bsonType for an object.
     *
     * @param object $data
     *
     * @return Int64|mixed
     */
    private function craftInt64($data)
    {
        if ($data->type !== 'long' || $data->value instanceof Int64) {
            return $data->value;
        }

        $class = Int64::class;

        $intAsString = sprintf((string) $data->value);
        $array = sprintf('a:1:{s:7:"integer";s:%d:"%s";}', strlen($intAsString), $intAsString);
        $int64 = sprintf('C:%d:"%s":%d:{%s}', strlen($class), $class, strlen($array), $array);

        return unserialize($int64);
    }

    private function createTestCollection($jsonSchema): void
    {
        $options = empty($jsonSchema) ? [] : ['validator' => ['$jsonSchema' => $jsonSchema]];
        $operation = new CreateCollection($this->getContext()->databaseName, $this->getContext()->collectionName, $options);
        $operation->execute($this->getPrimaryServer());
    }

    private function encryptCorpusValue(string $fieldName, stdClass $data, ClientEncryption $clientEncryption)
    {
        $encryptionOptions = [
            'algorithm' => $data->algo === 'rand' ? ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_RANDOM : ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
        ];

        switch ($data->kms) {
            case 'local':
                $keyId = 'LOCALAAAAAAAAAAAAAAAAA==';
                $keyAltName = 'local';
                break;
            case 'aws':
                $keyId = 'AWSAAAAAAAAAAAAAAAAAAA==';
                $keyAltName = 'aws';
                break;
            case 'azure':
                $keyId = 'AZUREAAAAAAAAAAAAAAAAA==';
                $keyAltName = 'azure';
                break;
            case 'gcp':
                $keyId = 'GCPAAAAAAAAAAAAAAAAAAA==';
                $keyAltName = 'gcp';
                break;

            default:
                throw new UnexpectedValueException('Unexpected KMS "%s"', $data->kms);
        }

        switch ($data->identifier) {
            case 'id':
                $encryptionOptions['keyId'] = new Binary(base64_decode($keyId), 4);
                break;

            case 'altname':
                $encryptionOptions['keyAltName'] = $keyAltName;
                break;

            default:
                throw new UnexpectedValueException('Unexpected value "%s" for identifier', $data->identifier);
        }

        if ($data->allowed) {
            try {
                $encrypted = $clientEncryption->encrypt($this->craftInt64($data), $encryptionOptions);
            } catch (EncryptionException $e) {
                $this->fail('Could not encrypt value for field ' . $fieldName . ': ' . $e->getMessage());
            }

            $this->assertEquals($data->value, $clientEncryption->decrypt($encrypted));

            return $encrypted;
        }

        try {
            $clientEncryption->encrypt($data->value, $encryptionOptions);
            $this->fail('Expected exception to be thrown');
        } catch (RuntimeException $e) {
        }

        return $data->value;
    }

    private function insertKeyVaultData(?array $keyVaultData = null): void
    {
        $context = $this->getContext();
        $collection = $context->selectCollection('keyvault', 'datakeys', ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)] + $context->defaultWriteOptions);
        $collection->drop();

        if (empty($keyVaultData)) {
            return;
        }

        $collection->insertMany($keyVaultData);
    }

    private function prepareCorpusData(string $fieldName, stdClass $data, ClientEncryption $clientEncryption)
    {
        if ($data->method === 'auto') {
            $data->value = $this->craftInt64($data);

            return $data;
        }

        $returnData = clone $data;
        $returnData->value = $this->encryptCorpusValue($fieldName, $data, $clientEncryption);

        return $data->allowed ? $returnData : $data;
    }
}
