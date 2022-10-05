<?php

namespace MongoDB\Tests\SpecTests;

use Closure;
use MongoDB\BSON\Binary;
use MongoDB\BSON\Int64;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\AuthenticationException;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use MongoDB\Driver\Exception\EncryptionException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Driver\WriteConcern;
use MongoDB\Tests\CommandObserver;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\SkippedTestError;
use stdClass;
use Throwable;
use UnexpectedValueException;

use function base64_decode;
use function basename;
use function count;
use function explode;
use function file_get_contents;
use function getenv;
use function glob;
use function in_array;
use function is_executable;
use function is_readable;
use function iterator_to_array;
use function json_decode;
use function sprintf;
use function str_repeat;
use function strlen;
use function substr;
use function unserialize;
use function version_compare;

use const DIRECTORY_SEPARATOR;
use const PATH_SEPARATOR;

/**
 * Client-side encryption spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption
 * @group csfle
 * @group serverless
 */
class ClientSideEncryptionSpecTest extends FunctionalTestCase
{
    public const LOCAL_MASTERKEY = 'Mng0NCt4ZHVUYUJCa1kxNkVyNUR1QURhZ2h2UzR2d2RrZzh0cFBwM3R6NmdWMDFBMUN3YkQ5aXRRMkhGRGdQV09wOGVNYUMxT2k3NjZKelhaQmRCZGJkTXVyZG9uSjFk';

    /** @var array */
    private static $incompleteTests = [
        'awsTemporary: Insert a document with auto encryption using the AWS provider with temporary credentials' => 'Not yet implemented (PHPC-1751)',
        'awsTemporary: Insert with invalid temporary credentials' => 'Not yet implemented (PHPC-1751)',
        'azureKMS: Insert a document with auto encryption using Azure KMS provider' => 'RHEL platform is missing Azure root certificate (PHPLIB-619)',
        'explain: Explain a find with deterministic encryption' => 'crypt_shared does not add apiVersion field to explain commands (PHPLIB-947, SERVER-69564)',
        'timeoutMS: timeoutMS applied to listCollections to get collection schema' => 'Not yet implemented (PHPC-1760)',
        'timeoutMS: remaining timeoutMS applied to find to get keyvault data' => 'Not yet implemented (PHPC-1760)',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->skipIfClientSideEncryptionIsNotSupported();

        if (! static::isCryptSharedLibAvailable() && ! static::isMongocryptdAvailable()) {
            $this->markTestSkipped('Neither crypt_shared nor mongocryptd are available');
        }
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

    public static function createTestClient(?string $uri = null, array $options = [], array $driverOptions = []): Client
    {
        if (isset($driverOptions['autoEncryption']) && getenv('CRYPT_SHARED_LIB_PATH')) {
            $driverOptions['autoEncryption']['extraOptions']['cryptSharedLibPath'] = getenv('CRYPT_SHARED_LIB_PATH');
        }

        return parent::createTestClient($uri, $options, $driverOptions);
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
    public function testClientSideEncryption(stdClass $test, ?array $runOn, array $data, ?stdClass $encryptedFields = null, ?array $keyVaultData = null, ?stdClass $jsonSchema = null, ?string $databaseName = null, ?string $collectionName = null): void
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

        // TODO: Remove this once SERVER-66901 is implemented (see: PHPLIB-884)
        if (isset($test->clientOptions->autoEncryptOpts->encryptedFieldsMap)) {
            $test->clientOptions->autoEncryptOpts->encryptedFieldsMap = $test->clientOptions->autoEncryptOpts->encryptedFieldsMap;
        }

        try {
            $context = Context::fromClientSideEncryption($test, $databaseName, $collectionName);
        } catch (SkippedTestError $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->setContext($context);

        self::insertKeyVaultData($context->getClient(), $keyVaultData);
        $this->dropTestAndOutcomeCollections(empty($encryptedFields) ? [] : ['encryptedFields' => $encryptedFields]);
        $this->createTestCollection($encryptedFields, $jsonSchema);
        $this->insertDataFixtures($data);

        if (isset($test->failPoint)) {
            $this->configureFailPoint($test->failPoint);
        }

        $context->useEncryptedClientIfConfigured = true;

        if (isset($test->expectations)) {
            $commandExpectations = CommandExpectations::fromClientSideEncryption($context->getClient(), $test->expectations);
            $commandExpectations->startMonitoring();
        }

        foreach ($test->operations as $operation) {
            Operation::fromClientSideEncryption($operation)->assert($this, $context);
        }

        if (isset($commandExpectations)) {
            $commandExpectations->stopMonitoring();
            $commandExpectations->assert($this, $context);
        }

        $context->useEncryptedClientIfConfigured = false;

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
            $encryptedFields = $json->encrypted_fields ?? null;
            $keyVaultData = $json->key_vault_data ?? null;
            $jsonSchema = $json->json_schema ?? null;
            $databaseName = $json->database_name ?? null;
            $collectionName = $json->collection_name ?? null;
            // phpcs:enable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[$name] = [$test, $runOn, $data, $encryptedFields, $keyVaultData, $jsonSchema, $databaseName, $collectionName];
            }
        }

        return $testArgs;
    }

    /**
     * Prose test 2: Data Key and Double Encryption
     *
     * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#data-key-and-double-encryption
     * @dataProvider dataKeyProvider
     */
    public function testDataKeyAndDoubleEncryption(string $providerName, $masterKey): void
    {
        $client = static::createTestClient();
        $client->selectCollection('db', 'coll')->drop();

        // Ensure that the key vault is dropped with a majority write concern
        self::insertKeyVaultData($client, []);

        $encryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
                'azure' => Context::getAzureCredentials(),
                'gcp' => Context::getGCPCredentials(),
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
                'kmip' => ['endpoint' => Context::getKmipEndpoint()],
            ],
            'tlsOptions' => [
                'kmip' => Context::getKmsTlsOptions(),
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

        $dataKeyId = null;
        $insertCommand = null;

        $keyAltName = $providerName . '_altname';

        (new CommandObserver())->observe(
            function () use ($clientEncryption, &$dataKeyId, $keyAltName, $providerName, $masterKey): void {
                $keyData = ['keyAltNames' => [$keyAltName]];
                if ($masterKey !== null) {
                    $keyData['masterKey'] = $masterKey;
                }

                $dataKeyId = $clientEncryption->createDataKey($providerName, $keyData);
            },
            function ($command) use (&$insertCommand): void {
                if ($command['started']->getCommandName() === 'insert') {
                    $insertCommand = $command['started']->getCommand();
                }
            }
        );

        $this->assertInstanceOf(Binary::class, $dataKeyId);
        $this->assertSame(Binary::TYPE_UUID, $dataKeyId->getType());

        $this->assertNotNull($insertCommand);
        $this->assertObjectHasAttribute('writeConcern', $insertCommand);
        $this->assertObjectHasAttribute('w', $insertCommand->writeConcern);
        $this->assertSame(WriteConcern::MAJORITY, $insertCommand->writeConcern->w);

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
            'kmip' => [
                'providerName' => 'kmip',
                'masterKey' => [],
            ],
        ];
    }

    /**
     * Prose test 3: External Key Vault
     *
     * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#external-key-vault-test
     * @testWith [false]
     *           [true]
     */
    public function testExternalKeyVault($withExternalKeyVault): void
    {
        $client = static::createTestClient();
        $client->selectCollection('db', 'coll')->drop();

        self::insertKeyVaultData($client, [
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/external/external-key.json')),
        ]);

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

        $clientEncryption->encrypt('test', [
            'algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
            'keyId' => new Binary(base64_decode('LOCALAAAAAAAAAAAAAAAAA=='), Binary::TYPE_UUID),
        ]);
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
     * Prose test 4: BSON Size Limits and Batch Splitting
     *
     * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#bson-size-limits-and-batch-splitting
     * @dataProvider provideBSONSizeLimitsAndBatchSplittingTests
     */
    public function testBSONSizeLimitsAndBatchSplitting(Closure $test): void
    {
        $client = static::createTestClient();

        $client->selectCollection('db', 'coll')->drop();
        $client->selectDatabase('db')->createCollection('coll', ['validator' => ['$jsonSchema' => $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/limits/limits-schema.json'))]]);

        self::insertKeyVaultData($client, [
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
     * Prose test 5: Views Are Prohibited
     *
     * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#views-are-prohibited
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
     * Prose test 6: BSON Corpus
     *
     * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#corpus-test
     * @testWith [true]
     *           [false]
     */
    public function testCorpus($schemaMap = true): void
    {
        $client = static::createTestClient();
        $client->selectDatabase('db')->dropCollection('coll');

        $schema = $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-schema.json'));

        if (! $schemaMap) {
            $client->selectDatabase('db')->createCollection('coll', ['validator' => ['$jsonSchema' => $schema]]);
        }

        self::insertKeyVaultData($client, [
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-local.json')),
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-aws.json')),
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-azure.json')),
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-gcp.json')),
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-kmip.json')),
        ]);

        $encryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
                'azure' => Context::getAzureCredentials(),
                'gcp' => Context::getGCPCredentials(),
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
                'kmip' => ['endpoint' => Context::getKmipEndpoint()],
            ],
            'tlsOptions' => [
                'kmip' => Context::getKmsTlsOptions(),
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
            'altname_kmip',
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
     * Prose test 7: Custom Endpoint
     *
     * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#custom-endpoint-test
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
                'kmip' => ['endpoint' => Context::getKmipEndpoint()],
            ],
            'tlsOptions' => [
                'kmip' => Context::getKmsTlsOptions(),
            ],
        ]);

        $clientEncryptionInvalid = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'azure' => Context::getAzureCredentials() + ['identityPlatformEndpoint' => 'doesnotexist.invalid:443'],
                'gcp' => Context::getGCPCredentials() + ['endpoint' => 'doesnotexist.invalid:443'],
                'kmip' => ['endpoint' => 'doesnotexist.local:5698'],
            ],
            'tlsOptions' => [
                'kmip' => Context::getKmsTlsOptions(),
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
        $kmipMasterKey = ['keyId' => '1'];

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
                $clientEncryption->createDataKey('aws', ['masterKey' => $awsMasterKey + ['endpoint' => 'kms.us-east-2.amazonaws.com']]);
            },
        ];

        yield 'Test 6' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($awsMasterKey): void {
                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#doesnotexist.invalid#');
                $clientEncryption->createDataKey('aws', ['masterKey' => $awsMasterKey + ['endpoint' => 'doesnotexist.invalid']]);
            },
        ];

        yield 'Test 7' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($azureMasterKey): void {
                $keyId = $clientEncryption->createDataKey('azure', ['masterKey' => $azureMasterKey]);
                $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
                $test->assertSame('test', $clientEncryption->decrypt($encrypted));

                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#doesnotexist.invalid#');
                $clientEncryptionInvalid->createDataKey('azure', ['masterKey' => $azureMasterKey]);
            },
        ];

        yield 'Test 8' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($gcpMasterKey): void {
                $keyId = $clientEncryption->createDataKey('gcp', ['masterKey' => $gcpMasterKey]);
                $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
                $test->assertSame('test', $clientEncryption->decrypt($encrypted));

                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#doesnotexist.invalid#');
                $clientEncryptionInvalid->createDataKey('gcp', ['masterKey' => $gcpMasterKey]);
            },
        ];

        yield 'Test 9' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($gcpMasterKey): void {
                $masterKey = $gcpMasterKey;
                $masterKey['endpoint'] = 'doesnotexist.invalid:443';

                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#Invalid KMS response#');
                $clientEncryption->createDataKey('gcp', ['masterKey' => $masterKey]);
            },
        ];

        yield 'Test 10' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($kmipMasterKey): void {
                $keyId = $clientEncryption->createDataKey('kmip', ['masterKey' => $kmipMasterKey]);
                $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
                $test->assertSame('test', $clientEncryption->decrypt($encrypted));

                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#doesnotexist.local#');
                $clientEncryptionInvalid->createDataKey('kmip', ['masterKey' => $kmipMasterKey]);
            },
        ];

        yield 'Test 11' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($kmipMasterKey): void {
                $kmipMasterKey['endpoint'] = Context::getKmipEndpoint();

                $keyId = $clientEncryption->createDataKey('kmip', ['masterKey' => $kmipMasterKey]);
                $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
                $test->assertSame('test', $clientEncryption->decrypt($encrypted));
            },
        ];

        yield 'Test 12' => [
            static function (self $test, ClientEncryption $clientEncryption, ClientEncryption $clientEncryptionInvalid) use ($kmipMasterKey): void {
                $kmipMasterKey['endpoint'] = 'doesnotexist.local:5698';

                $test->expectException(RuntimeException::class);
                $test->expectExceptionMessageMatches('#doesnotexist.local#');
                $clientEncryption->createDataKey('kmip', ['masterKey' => $kmipMasterKey]);
            },
        ];
    }

    /**
     * Prose test 8: Bypass Spawning mongocryptd (via mongocryptdBypassSpawn)
     *
     * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#via-mongocryptdbypassspawn
     */
    public function testBypassSpawningMongocryptdViaBypassSpawn(): void
    {
        /* If crypt_shared is available it will likely already have been loaded
         * by a previous test so there is no way to prevent it from being used.
         * Since CSFLE prefers crypt_shared to mongocryptd there is reason to
         * run any of the "bypass spawning" tests (see also: MONGOCRYPT-421). */
        if (static::isCryptSharedLibAvailable()) {
            $this->markTestSkipped('Bypass spawning of mongocryptd cannot be tested when crypt_shared is available');
        }

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

        // Disable adding cryptSharedLibPath, as it may interfere with this test
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
     * Prose test 8: Bypass spawning mongocryptd (via bypassAutoEncryption)
     *
     * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#via-bypassautoencryption
     */
    public function testBypassSpawningMongocryptdViaBypassAutoEncryption(): void
    {
        if (static::isCryptSharedLibAvailable()) {
            $this->markTestSkipped('Bypass spawning of mongocryptd cannot be tested when crypt_shared is available');
        }

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

        // Disable adding cryptSharedLibPath, as it may interfere with this test
        $clientEncrypted = static::createTestClient(null, [], ['autoEncryption' => $autoEncryptionOpts]);

        $clientEncrypted->selectCollection('db', 'coll')->insertOne(['unencrypted' => 'test']);

        $clientMongocryptd = static::createTestClient('mongodb://localhost:27021');

        $this->expectException(ConnectionTimeoutException::class);
        $clientMongocryptd->selectDatabase('db')->command(['ping' => 1]);
    }

    /**
     * Prose test 8: Bypass spawning mongocryptd (via bypassQueryAnalysis)
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#via-bypassqueryanalysis
     */
    public function testBypassSpawningMongocryptdViaBypassQueryAnalysis(): void
    {
        if (static::isCryptSharedLibAvailable()) {
            $this->markTestSkipped('Bypass spawning of mongocryptd cannot be tested when crypt_shared is available');
        }

        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
            'bypassQueryAnalysis' => true,
            'extraOptions' => [
                'mongocryptdSpawnArgs' => ['--pidfilepath=bypass-spawning-mongocryptd.pid', '--port=27021'],
            ],
        ];

        // Disable adding cryptSharedLibPath, as it may interfere with this test
        $clientEncrypted = static::createTestClient(null, [], ['autoEncryption' => $autoEncryptionOpts]);

        $clientEncrypted->selectCollection('db', 'coll')->insertOne(['unencrypted' => 'test']);

        $clientMongocryptd = static::createTestClient('mongodb://localhost:27021');

        $this->expectException(ConnectionTimeoutException::class);
        $clientMongocryptd->selectDatabase('db')->command(['ping' => 1]);
    }

    /**
     * Prose test 10: KMS TLS Tests (Invalid KMS Certificate)
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#invalid-kms-certificate
     */
    public function testInvalidKmsCertificate(): void
    {
        $client = static::createTestClient();

        $clientEncryption = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['aws' => Context::getAWSCredentials()],
            'tlsOptions' => ['aws' => Context::getKmsTlsOptions()],
        ]);

        $this->expectException(ConnectionException::class);
        // Note: this assumes an OpenSSL error message
        $this->expectExceptionMessageMatches('#certificate has expired#');

        $clientEncryption->createDataKey('aws', [
            'masterKey' => [
                'region' => 'us-east-1',
                'key' => 'arn:aws:kms:us-east-1:579766882180:key/89fcc2c4-08b0-4bd9-9f25-e30687b580d0',
                'endpoint' => self::getEnv('KMS_ENDPOINT_EXPIRED'),
            ],
        ]);
    }

    /**
     * Prose test 10: KMS TLS Tests (Invalid Hostname in KMS Certificate)
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#invalid-hostname-in-kms-certificate
     */
    public function testInvalidHostnameInKmsCertificate(): void
    {
        $client = static::createTestClient();

        $clientEncryption = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['aws' => Context::getAWSCredentials()],
            'tlsOptions' => ['aws' => Context::getKmsTlsOptions()],
        ]);

        $this->expectException(ConnectionException::class);
        // Note: this assumes an OpenSSL error message
        $this->expectExceptionMessageMatches('#IP address mismatch#');

        $clientEncryption->createDataKey('aws', [
            'masterKey' => [
                'region' => 'us-east-1',
                'key' => 'arn:aws:kms:us-east-1:579766882180:key/89fcc2c4-08b0-4bd9-9f25-e30687b580d0',
                'endpoint' => self::getEnv('KMS_ENDPOINT_WRONG_HOST'),
            ],
        ]);
    }

    /**
     * Prose test 11: KMS TLS Options
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#kms-tls-options-tests
     * @dataProvider provideKmsTlsOptionsTests
     */
    public function testKmsTlsOptions(Closure $test): void
    {
        $client = static::createTestClient();

        $clientEncryptionNoClientCert = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
                'azure' => Context::getAzureCredentials() + ['identityPlatformEndpoint' => self::getEnv('KMS_ENDPOINT_REQUIRE_CLIENT_CERT')],
                'gcp' => Context::getGCPCredentials() + ['endpoint' => self::getEnv('KMS_ENDPOINT_REQUIRE_CLIENT_CERT')],
                'kmip' => ['endpoint' => Context::getKmipEndpoint()],
            ],
            'tlsOptions' => [
                'aws' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
                'azure' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
                'gcp' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
                'kmip' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
            ],
        ]);

        $clientEncryptionWithTls = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
                'azure' => Context::getAzureCredentials() + ['identityPlatformEndpoint' => self::getEnv('KMS_ENDPOINT_REQUIRE_CLIENT_CERT')],
                'gcp' => Context::getGCPCredentials() + ['endpoint' => self::getEnv('KMS_ENDPOINT_REQUIRE_CLIENT_CERT')],
                'kmip' => ['endpoint' => Context::getKmipEndpoint()],
            ],
            'tlsOptions' => [
                'aws' => Context::getKmsTlsOptions(),
                'azure' => Context::getKmsTlsOptions(),
                'gcp' => Context::getKmsTlsOptions(),
                'kmip' => Context::getKmsTlsOptions(),
            ],
        ]);

        $clientEncryptionExpired = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
                'azure' => Context::getAzureCredentials() + ['identityPlatformEndpoint' => self::getEnv('KMS_ENDPOINT_EXPIRED')],
                'gcp' => Context::getGCPCredentials() + ['endpoint' => self::getEnv('KMS_ENDPOINT_EXPIRED')],
                'kmip' => ['endpoint' => self::getEnv('KMS_ENDPOINT_EXPIRED')],
            ],
            'tlsOptions' => [
                'aws' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
                'azure' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
                'gcp' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
                'kmip' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
            ],
        ]);

        $clientEncryptionInvalidHostname = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
                'azure' => Context::getAzureCredentials() + ['identityPlatformEndpoint' => self::getEnv('KMS_ENDPOINT_WRONG_HOST')],
                'gcp' => Context::getGCPCredentials() + ['endpoint' => self::getEnv('KMS_ENDPOINT_WRONG_HOST')],
                'kmip' => ['endpoint' => self::getEnv('KMS_ENDPOINT_WRONG_HOST')],
            ],
            'tlsOptions' => [
                'aws' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
                'azure' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
                'gcp' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
                'kmip' => ['tlsCAFile' => getenv('KMS_TLS_CA_FILE')],
            ],
        ]);

        $test($this, $clientEncryptionNoClientCert, $clientEncryptionWithTls, $clientEncryptionExpired, $clientEncryptionInvalidHostname);
    }

    public static function provideKmsTlsOptionsTests()
    {
        $awsMasterKey = ['region' => 'us-east-1', 'key' => 'arn:aws:kms:us-east-1:579766882180:key/89fcc2c4-08b0-4bd9-9f25-e30687b580d0'];
        $azureMasterKey = ['keyVaultEndpoint' => 'doesnotexist.local', 'keyName' => 'foo'];
        $gcpMasterKey = ['projectId' => 'foo', 'location' => 'bar', 'keyRing' => 'baz', 'keyName' => 'foo'];
        $kmipMasterKey = [];

        // Note: expected exception messages below assume OpenSSL is used

        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-1-aws
        yield 'AWS: client_encryption_no_client_cert' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($awsMasterKey): void {
                $test->expectException(ConnectionException::class);
                // Avoid asserting exception message for failed TLS handshake since it may be inconsistent
                $clientEncryptionNoClientCert->createDataKey('aws', ['masterKey' => $awsMasterKey + ['endpoint' => self::getEnv('KMS_ENDPOINT_REQUIRE_CLIENT_CERT')]]);
            },
        ];

        yield 'AWS: client_encryption_with_tls' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($awsMasterKey): void {
                $test->expectException(EncryptionException::class);
                $test->expectExceptionMessageMatches('#parse error#');
                $clientEncryptionWithTls->createDataKey('aws', ['masterKey' => $awsMasterKey + ['endpoint' => self::getEnv('KMS_ENDPOINT_REQUIRE_CLIENT_CERT')]]);
            },
        ];

        yield 'AWS: client_encryption_expired' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($awsMasterKey): void {
                $test->expectException(ConnectionException::class);
                $test->expectExceptionMessageMatches('#certificate has expired#');
                $clientEncryptionExpired->createDataKey('aws', ['masterKey' => $awsMasterKey + ['endpoint' => self::getEnv('KMS_ENDPOINT_EXPIRED')]]);
            },
        ];

        yield 'AWS: client_encryption_invalid_hostname' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($awsMasterKey): void {
                $test->expectException(ConnectionException::class);
                $test->expectExceptionMessageMatches('#IP address mismatch#');
                $clientEncryptionInvalidHostname->createDataKey('aws', ['masterKey' => $awsMasterKey + ['endpoint' => self::getEnv('KMS_ENDPOINT_WRONG_HOST')]]);
            },
        ];

        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-2-azure
        yield 'Azure: client_encryption_no_client_cert' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($azureMasterKey): void {
                $test->expectException(ConnectionException::class);
                // Avoid asserting exception message for failed TLS handshake since it may be inconsistent
                $clientEncryptionNoClientCert->createDataKey('azure', ['masterKey' => $azureMasterKey]);
            },
        ];

        yield 'Azure: client_encryption_with_tls' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($azureMasterKey): void {
                $test->expectException(EncryptionException::class);
                $test->expectExceptionMessageMatches('#HTTP status=404#');
                $clientEncryptionWithTls->createDataKey('azure', ['masterKey' => $azureMasterKey]);
            },
        ];

        yield 'Azure: client_encryption_expired' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($azureMasterKey): void {
                $test->expectException(ConnectionException::class);
                $test->expectExceptionMessageMatches('#certificate has expired#');
                $clientEncryptionExpired->createDataKey('azure', ['masterKey' => $azureMasterKey]);
            },
        ];

        yield 'Azure: client_encryption_invalid_hostname' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($azureMasterKey): void {
                $test->expectException(ConnectionException::class);
                $test->expectExceptionMessageMatches('#IP address mismatch#');
                $clientEncryptionInvalidHostname->createDataKey('azure', ['masterKey' => $azureMasterKey]);
            },
        ];

        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-3-gcp
        yield 'GCP: client_encryption_no_client_cert' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($gcpMasterKey): void {
                $test->expectException(ConnectionException::class);
                // Avoid asserting exception message for failed TLS handshake since it may be inconsistent
                $clientEncryptionNoClientCert->createDataKey('gcp', ['masterKey' => $gcpMasterKey]);
            },
        ];

        yield 'GCP: client_encryption_with_tls' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($gcpMasterKey): void {
                $test->expectException(EncryptionException::class);
                $test->expectExceptionMessageMatches('#HTTP status=404#');
                $clientEncryptionWithTls->createDataKey('gcp', ['masterKey' => $gcpMasterKey]);
            },
        ];

        yield 'GCP: client_encryption_expired' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($gcpMasterKey): void {
                $test->expectException(ConnectionException::class);
                $test->expectExceptionMessageMatches('#certificate has expired#');
                $clientEncryptionExpired->createDataKey('gcp', ['masterKey' => $gcpMasterKey]);
            },
        ];

        yield 'GCP: client_encryption_invalid_hostname' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($gcpMasterKey): void {
                $test->expectException(ConnectionException::class);
                $test->expectExceptionMessageMatches('#IP address mismatch#');
                $clientEncryptionInvalidHostname->createDataKey('gcp', ['masterKey' => $gcpMasterKey]);
            },
        ];

        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-4-kmip
        yield 'KMIP: client_encryption_no_client_cert' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($kmipMasterKey): void {
                $test->expectException(ConnectionException::class);
                // Avoid asserting exception message for failed TLS handshake since it may be inconsistent
                $clientEncryptionNoClientCert->createDataKey('kmip', ['masterKey' => $kmipMasterKey]);
            },
        ];

        yield 'KMIP: client_encryption_with_tls' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($kmipMasterKey): void {
                $keyId = $clientEncryptionWithTls->createDataKey('kmip', ['masterKey' => $kmipMasterKey]);
                $test->assertInstanceOf(Binary::class, $keyId);
            },
        ];

        yield 'KMIP: client_encryption_expired' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($kmipMasterKey): void {
                $test->expectException(ConnectionException::class);
                $test->expectExceptionMessageMatches('#certificate has expired#');
                $clientEncryptionExpired->createDataKey('kmip', ['masterKey' => $kmipMasterKey]);
            },
        ];

        yield 'KMIP: client_encryption_invalid_hostname' => [
            static function (self $test, ClientEncryption $clientEncryptionNoClientCert, ClientEncryption $clientEncryptionWithTls, ClientEncryption $clientEncryptionExpired, ClientEncryption $clientEncryptionInvalidHostname) use ($kmipMasterKey): void {
                $test->expectException(ConnectionException::class);
                $test->expectExceptionMessageMatches('#IP address mismatch#');
                $clientEncryptionInvalidHostname->createDataKey('kmip', ['masterKey' => $kmipMasterKey]);
            },
        ];
    }

    /**
     * Prose test 12: Explicit Encryption
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#explicit-encryption
     * @dataProvider provideExplicitEncryptionTests
     */
    public function testExplicitEncryption(Closure $test): void
    {
        if ($this->isStandalone() || ($this->isShardedCluster() && ! $this->isShardedClusterUsingReplicasets())) {
            $this->markTestSkipped('Explicit encryption tests require replica sets');
        }

        if (version_compare($this->getServerVersion(), '6.0.0', '<')) {
            $this->markTestSkipped('Explicit encryption tests require MongoDB 6.0 or later');
        }

        // Test setup
        $encryptedFields = $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/etc/data/encryptedFields.json'));
        $key1Document = $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/etc/data/keys/key1-document.json'));
        $key1Id = $key1Document->_id;

        $client = static::createTestClient();

        $database = $client->selectDatabase('db');
        $database->dropCollection('explicit_encryption', ['encryptedFields' => $encryptedFields]);
        $database->createCollection('explicit_encryption', ['encryptedFields' => $encryptedFields]);

        $database = $client->selectDatabase('keyvault');
        $database->dropCollection('datakeys');
        $database->createCollection('datakeys');

        $client->selectCollection('keyvault', 'datakeys')->insertOne($key1Document, ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]);

        $keyVaultClient = static::createTestClient();

        $clientEncryption = new ClientEncryption([
            'keyVaultClient' => $keyVaultClient->getManager(),
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)]],
        ]);

        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)]],
            'bypassQueryAnalysis' => true,
        ];

        $encryptedClient = static::createTestClient(null, [], ['autoEncryption' => $autoEncryptionOpts]);

        $test($this, $clientEncryption, $encryptedClient, $keyVaultClient, $key1Id);
    }

    public static function provideExplicitEncryptionTests()
    {
        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-1-can-insert-encrypted-indexed-and-find
        yield 'Case 1: can insert encrypted indexed and find' => [
            static function (self $test, ClientEncryption $clientEncryption, Client $encryptedClient, Client $keyVaultClient, Binary $key1Id): void {
                $value = 'encrypted indexed value';

                $insertPayload = $clientEncryption->encrypt($value, [
                    'keyId' => $key1Id,
                    'algorithm' => ClientEncryption::ALGORITHM_INDEXED,
                    'contentionFactor' => 0,
                ]);

                $collection = $encryptedClient->selectCollection('db', 'explicit_encryption');
                $collection->insertOne(['encryptedIndexed' => $insertPayload]);

                $findPayload = $clientEncryption->encrypt($value, [
                    'keyId' => $key1Id,
                    'algorithm' => ClientEncryption::ALGORITHM_INDEXED,
                    'queryType' => ClientEncryption::QUERY_TYPE_EQUALITY,
                    'contentionFactor' => 0,
                ]);

                $results = $collection->find(['encryptedIndexed' => $findPayload])->toArray();

                $test->assertCount(1, $results);
                $test->assertSame($value, $results[0]['encryptedIndexed']);
            },
        ];

        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-2-can-insert-encrypted-indexed-and-find-with-non-zero-contention
        yield 'Case 2: can insert encrypted indexed and find with non-zero contention' => [
            static function (self $test, ClientEncryption $clientEncryption, Client $encryptedClient, Client $keyVaultClient, Binary $key1Id): void {
                $value = 'encrypted indexed value';

                $collection = $encryptedClient->selectCollection('db', 'explicit_encryption');

                for ($i = 0; $i < 10; $i++) {
                    $insertPayload = $clientEncryption->encrypt($value, [
                        'keyId' => $key1Id,
                        'algorithm' => ClientEncryption::ALGORITHM_INDEXED,
                        'contentionFactor' => 10,
                    ]);

                    $collection->insertOne(['encryptedIndexed' => $insertPayload]);
                }

                $findPayload = $clientEncryption->encrypt($value, [
                    'keyId' => $key1Id,
                    'algorithm' => ClientEncryption::ALGORITHM_INDEXED,
                    'queryType' => ClientEncryption::QUERY_TYPE_EQUALITY,
                    'contentionFactor' => 0,
                ]);

                $results = $collection->find(['encryptedIndexed' => $findPayload])->toArray();

                $test->assertLessThan(10, count($results));

                foreach ($results as $result) {
                    $test->assertSame($value, $result['encryptedIndexed']);
                }

                $findPayload2 = $clientEncryption->encrypt($value, [
                    'keyId' => $key1Id,
                    'algorithm' => ClientEncryption::ALGORITHM_INDEXED,
                    'queryType' => ClientEncryption::QUERY_TYPE_EQUALITY,
                    'contentionFactor' => 10,
                ]);

                $results = $collection->find(['encryptedIndexed' => $findPayload2])->toArray();

                $test->assertCount(10, $results);

                foreach ($results as $result) {
                    $test->assertSame($value, $result['encryptedIndexed']);
                }
            },
        ];

        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-3-can-insert-encrypted-unindexed
        yield 'Case 3: can insert encrypted unindexed' => [
            static function (self $test, ClientEncryption $clientEncryption, Client $encryptedClient, Client $keyVaultClient, Binary $key1Id): void {
                $value = 'encrypted unindexed value';

                $insertPayload = $clientEncryption->encrypt($value, [
                    'keyId' => $key1Id,
                    'algorithm' => ClientEncryption::ALGORITHM_UNINDEXED,
                ]);

                $collection = $encryptedClient->selectCollection('db', 'explicit_encryption');
                $collection->insertOne(['_id' => 1, 'encryptedUnindexed' => $insertPayload]);

                $results = $collection->find(['_id' => 1])->toArray();

                $test->assertCount(1, $results);
                $test->assertSame($value, $results[0]['encryptedUnindexed']);
            },
        ];

        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-4-can-roundtrip-encrypted-indexed
        yield 'Case 4: can roundtrip encrypted indexed' => [
            static function (self $test, ClientEncryption $clientEncryption, Client $encryptedClient, Client $keyVaultClient, Binary $key1Id): void {
                $value = 'encrypted indexed value';

                $payload = $clientEncryption->encrypt($value, [
                    'keyId' => $key1Id,
                    'algorithm' => ClientEncryption::ALGORITHM_INDEXED,
                    'contentionFactor' => 0,
                ]);

                $test->assertSame($value, $clientEncryption->decrypt($payload));
            },
        ];

        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-5-can-roundtrip-encrypted-unindexed
        yield 'Case 5: can roundtrip encrypted unindexed' => [
            static function (self $test, ClientEncryption $clientEncryption, Client $encryptedClient, Client $keyVaultClient, Binary $key1Id): void {
                $value = 'encrypted unindexed value';

                $payload = $clientEncryption->encrypt($value, [
                    'keyId' => $key1Id,
                    'algorithm' => ClientEncryption::ALGORITHM_UNINDEXED,
                ]);

                $test->assertSame($value, $clientEncryption->decrypt($payload));
            },
        ];
    }

    /**
     * Prose test 13: Unique Index on keyAltNames
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#unique-index-on-keyaltnames
     * @dataProvider provideUniqueIndexOnKeyAltNamesTests
     */
    public function testUniqueIndexOnKeyAltNames(Closure $test): void
    {
        // Test setup
        $client = static::createTestClient();

        // Ensure that the key vault is dropped with a majority write concern
        self::insertKeyVaultData($client, []);

        $client->selectCollection('keyvault', 'datakeys')->createIndex(
            ['keyAltNames' => 1],
            [
                'unique' => true,
                'partialFilterExpression' => ['keyAltNames' => ['$exists' => true]],
                'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
            ]
        );

        $clientEncryption = new ClientEncryption([
            'keyVaultClient' => $client->getManager(),
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)]],
        ]);

        $clientEncryption->createDataKey('local', ['keyAltNames' => ['def']]);

        $test($this, $client, $clientEncryption);
    }

    public static function provideUniqueIndexOnKeyAltNamesTests()
    {
        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-1-createdatakey
        yield 'Case 1: createDataKey()' => [
            static function (self $test, Client $client, ClientEncryption $clientEncryption): void {
                $clientEncryption->createDataKey('local', ['keyAltNames' => ['abc']]);

                try {
                    $clientEncryption->createDataKey('local', ['keyAltNames' => ['abc']]);
                    $test->fail('Expected exception to be thrown');
                } catch (ServerException $e) {
                    $test->assertSame(11000 /* DuplicateKey */, $e->getCode());
                }

                try {
                    $clientEncryption->createDataKey('local', ['keyAltNames' => ['def']]);
                    $test->fail('Expected exception to be thrown');
                } catch (ServerException $e) {
                    $test->assertSame(11000 /* DuplicateKey */, $e->getCode());
                }
            },
        ];

        // See: https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-2-addkeyaltname
        yield 'Case 2: addKeyAltName()' => [
            static function (self $test, Client $client, ClientEncryption $clientEncryption): void {
                $keyId = $clientEncryption->createDataKey('local');

                $keyBeforeUpdate = $clientEncryption->addKeyAltName($keyId, 'abc');
                $test->assertObjectNotHasAttribute('keyAltNames', $keyBeforeUpdate);

                $keyBeforeUpdate = $clientEncryption->addKeyAltName($keyId, 'abc');
                $test->assertObjectHasAttribute('keyAltNames', $keyBeforeUpdate);
                $test->assertIsArray($keyBeforeUpdate->keyAltNames);
                $test->assertContains('abc', $keyBeforeUpdate->keyAltNames);

                try {
                    $clientEncryption->addKeyAltName($keyId, 'def');
                    $test->fail('Expected exception to be thrown');
                } catch (ServerException $e) {
                    $test->assertSame(11000 /* DuplicateKey */, $e->getCode());
                }

                $originalKeyId = $clientEncryption->getKeyByAltName('def')->_id;

                $originalKeyBeforeUpdate = $clientEncryption->addKeyAltName($originalKeyId, 'def');
                $test->assertObjectHasAttribute('keyAltNames', $originalKeyBeforeUpdate);
                $test->assertIsArray($originalKeyBeforeUpdate->keyAltNames);
                $test->assertContains('def', $originalKeyBeforeUpdate->keyAltNames);
            },
        ];
    }

    /**
     * Prose test 14: Decryption Events
     *
     * @see https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#decryption-events
     * @dataProvider provideDecryptionEventsTests
     */
    public function testDecryptionEvents(Closure $test): void
    {
        // Test setup
        $setupClient = static::createTestClient();
        $setupClient->selectCollection('db', 'decryption_events')->drop();

        // Ensure that the key vault is dropped with a majority write concern
        self::insertKeyVaultData($setupClient, []);

        $clientEncryption = new ClientEncryption([
            'keyVaultClient' => $setupClient->getManager(),
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)]],
        ]);

        $keyId = $clientEncryption->createDataKey('local');

        $cipherText = $clientEncryption->encrypt('hello', [
            'keyId' => $keyId,
            'algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
        ]);

        // Flip the last byte in the encrypted string
        $malformedCipherText = new Binary(substr($cipherText->getData(), 0, -1) . ~$cipherText->getData()[-1], Binary::TYPE_ENCRYPTED);

        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)]],
        ];

        $encryptedClient = static::createTestClient(null, ['retryReads' => false], ['autoEncryption' => $autoEncryptionOpts]);

        $subscriber = new class implements CommandSubscriber {
            public $lastAggregateReply;
            public $lastAggregateError;

            public function commandStarted(CommandStartedEvent $event): void
            {
            }

            public function commandSucceeded(CommandSucceededEvent $event): void
            {
                if ($event->getCommandName() === 'aggregate') {
                    $this->lastAggregateReply = $event->getReply();
                }
            }

            public function commandFailed(CommandFailedEvent $event): void
            {
                if ($event->getCommandName() === 'aggregate') {
                    $this->lastAggregateError = $event->getError();
                }
            }
        };

        $encryptedClient->getManager()->addSubscriber($subscriber);

        $test($this, $setupClient, $clientEncryption, $encryptedClient, $subscriber, $cipherText, $malformedCipherText);

        $encryptedClient->getManager()->removeSubscriber($subscriber);
    }

    public static function provideDecryptionEventsTests()
    {
        // See: https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#case-1-command-error
        yield 'Case 1: Command Error' => [
            static function (self $test, Client $setupClient, ClientEncryption $clientEncryption, Client $encryptedClient, CommandSubscriber $subscriber, Binary $cipherText, Binary $malformedCipherText): void {
                $setupClient->selectDatabase('admin')->command([
                    'configureFailPoint' => 'failCommand',
                    'mode' => ['times' => 1],
                    'data' => [
                        'errorCode' => 123,
                        'failCommands' => ['aggregate'],
                    ],
                ]);

                try {
                    $encryptedClient->selectCollection('db', 'decryption_events')->aggregate([]);
                    $test->fail('Expected exception to be thrown');
                } catch (CommandException $e) {
                    $test->assertSame(123, $e->getCode());
                }

                $test->assertNotNull($subscriber->lastAggregateError);
            },
        ];

        // See: https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#case-2-network-error
        yield 'Case 2: Network Error' => [
            static function (self $test, Client $setupClient, ClientEncryption $clientEncryption, Client $encryptedClient, CommandSubscriber $subscriber, Binary $cipherText, Binary $malformedCipherText): void {
                $setupClient->selectDatabase('admin')->command([
                    'configureFailPoint' => 'failCommand',
                    'mode' => ['times' => 1],
                    'data' => [
                        'closeConnection' => true,
                        'failCommands' => ['aggregate'],
                    ],
                ]);

                try {
                    $encryptedClient->selectCollection('db', 'decryption_events')->aggregate([]);
                    $test->fail('Expected exception to be thrown');
                } catch (ConnectionTimeoutException $e) {
                    $test->addToAssertionCount(1);
                }

                $test->assertNotNull($subscriber->lastAggregateError);
            },
        ];

        // See: https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#case-3-decrypt-error
        yield 'Case 3: Decrypt Error' => [
            static function (self $test, Client $setupClient, ClientEncryption $clientEncryption, Client $encryptedClient, CommandSubscriber $subscriber, Binary $cipherText, Binary $malformedCipherText): void {
                $collection = $encryptedClient->selectCollection('db', 'decryption_events');

                $collection->insertOne(['encrypted' => $malformedCipherText]);

                try {
                    $collection->aggregate([]);
                    $test->fail('Expected exception to be thrown');
                } catch (EncryptionException $e) {
                    $test->assertStringContainsString('HMAC validation failure', $e->getMessage());
                }

                $test->assertNotNull($subscriber->lastAggregateReply);
                $test->assertEquals($malformedCipherText, $subscriber->lastAggregateReply->cursor->firstBatch[0]->encrypted ?? null);
            },
        ];

        // See: https://github.com/mongodb/specifications/tree/master/source/client-side-encryption/tests#case-4-decrypt-success
        yield 'Case 4: Decrypt Success' => [
            static function (self $test, Client $setupClient, ClientEncryption $clientEncryption, Client $encryptedClient, CommandSubscriber $subscriber, Binary $cipherText, Binary $malformedCipherText): void {
                $collection = $encryptedClient->selectCollection('db', 'decryption_events');

                $collection->insertOne(['encrypted' => $cipherText]);
                $collection->aggregate([]);

                $test->assertNotNull($subscriber->lastAggregateReply);
                $test->assertEquals($cipherText, $subscriber->lastAggregateReply->cursor->firstBatch[0]->encrypted ?? null);
            },
        ];
    }

    /**
     * Prose test 16: RewrapManyDataKey
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#rewrap
     * @dataProvider provideRewrapManyDataKeySrcAndDstProviders
     */
    public function testRewrapManyDataKey(string $srcProvider, string $dstProvider): void
    {
        $providerMasterKeys = [
            'aws' => ['region' => 'us-east-1', 'key' => 'arn:aws:kms:us-east-1:579766882180:key/89fcc2c4-08b0-4bd9-9f25-e30687b580d0'],
            'azure' => ['keyVaultEndpoint' => 'key-vault-csfle.vault.azure.net', 'keyName' => 'key-name-csfle'],
            'gcp' => ['projectId' => 'devprod-drivers', 'location' => 'global', 'keyRing' => 'key-ring-csfle', 'keyName' => 'key-name-csfle'],
            'kmip' => [],
        ];

        // Test setup
        $client = static::createTestClient();

        // Ensure that the key vault is dropped with a majority write concern
        self::insertKeyVaultData($client, []);

        $clientEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
                'azure' => Context::getAzureCredentials(),
                'gcp' => Context::getGCPCredentials(),
                'kmip' => ['endpoint' => Context::getKmipEndpoint()],
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
            'tlsOptions' => [
                'kmip' => Context::getKmsTlsOptions(),
            ],
        ];

        $clientEncryption1 = $client->createClientEncryption($clientEncryptionOpts);

        $createDataKeyOpts = [];

        if (isset($providerMasterKeys[$srcProvider])) {
            $createDataKeyOpts['masterKey'] = $providerMasterKeys[$srcProvider];
        }

        $keyId = $clientEncryption1->createDataKey($srcProvider, $createDataKeyOpts);

        $ciphertext = $clientEncryption1->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);

        $clientEncryption2 = $client->createClientEncryption($clientEncryptionOpts);

        $rewrapManyDataKeyOpts = ['provider' => $dstProvider];

        if (isset($providerMasterKeys[$dstProvider])) {
            $rewrapManyDataKeyOpts['masterKey'] = $providerMasterKeys[$dstProvider];
        }

        $result = $clientEncryption2->rewrapManyDataKey([], $rewrapManyDataKeyOpts);

        $this->assertObjectHasAttribute('bulkWriteResult', $result);
        $this->assertIsObject($result->bulkWriteResult);
        // libmongoc uses different field names for its BulkWriteResult
        $this->assertObjectHasAttribute('nModified', $result->bulkWriteResult);
        $this->assertSame(1, $result->bulkWriteResult->nModified);

        $this->assertSame('test', $clientEncryption1->decrypt($ciphertext));
        $this->assertSame('test', $clientEncryption2->decrypt($ciphertext));
    }

    public static function provideRewrapManyDataKeySrcAndDstProviders()
    {
        $providers = ['aws', 'azure', 'gcp', 'kmip', 'local'];

        foreach ($providers as $srcProvider) {
            foreach ($providers as $dstProvider) {
                yield [$srcProvider, $dstProvider];
            }
        }
    }

    private function createInt64(string $value): Int64
    {
        $array = sprintf('a:1:{s:7:"integer";s:%d:"%s";}', strlen($value), $value);
        $int64 = sprintf('C:%d:"%s":%d:{%s}', strlen(Int64::class), Int64::class, strlen($array), $array);

        return unserialize($int64);
    }

    private function createTestCollection(?stdClass $encryptedFields = null, ?stdClass $jsonSchema = null): void
    {
        $context = $this->getContext();
        $options = $context->defaultWriteOptions;

        if (! empty($encryptedFields)) {
            $options['encryptedFields'] = $encryptedFields;
        }

        if (! empty($jsonSchema)) {
            $options['validator'] = ['$jsonSchema' => $jsonSchema];
        }

        $context->getDatabase()->createCollection($context->collectionName, $options);
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
            case 'kmip':
                $keyId = 'KMIPAAAAAAAAAAAAAAAAAA==';
                $keyAltName = 'kmip';
                break;

            default:
                throw new UnexpectedValueException(sprintf('Unexpected KMS "%s"', $data->kms));
        }

        switch ($data->identifier) {
            case 'id':
                $encryptionOptions['keyId'] = new Binary(base64_decode($keyId), 4);
                break;

            case 'altname':
                $encryptionOptions['keyAltName'] = $keyAltName;
                break;

            default:
                throw new UnexpectedValueException(sprintf('Unexpected value "%s" for identifier', $data->identifier));
        }

        if ($data->allowed) {
            try {
                /* Note: workaround issue where mongocryptd refuses to encrypt
                 * 32-bit integers if schemaMap defines a "long" BSON type. */
                $value = $data->type === 'long' && ! $data->value instanceof Int64
                    ? $this->createInt64($data->value)
                    : $data->value;

                $encrypted = $clientEncryption->encrypt($value, $encryptionOptions);
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

    private static function getEnv(string $name): string
    {
        $value = getenv($name);

        if ($value === false) {
            Assert::markTestSkipped(sprintf('Environment variable "%s" is not defined', $name));
        }

        return $value;
    }

    private static function insertKeyVaultData(Client $client, ?array $keyVaultData = null): void
    {
        $collection = $client->selectCollection('keyvault', 'datakeys', ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)]);
        $collection->drop();

        if (empty($keyVaultData)) {
            return;
        }

        $collection->insertMany($keyVaultData);
    }

    private function prepareCorpusData(string $fieldName, stdClass $data, ClientEncryption $clientEncryption)
    {
        if ($data->method === 'auto') {
            /* Note: workaround issue where mongocryptd refuses to encrypt
             * 32-bit integers if schemaMap defines a "long" BSON type. */
            if ($data->type === 'long' && ! $data->value instanceof Int64) {
                $data->value = $this->createInt64($data->value);
            }

            return $data;
        }

        $returnData = clone $data;
        $returnData->value = $this->encryptCorpusValue($fieldName, $data, $clientEncryption);

        return $data->allowed ? $returnData : $data;
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
