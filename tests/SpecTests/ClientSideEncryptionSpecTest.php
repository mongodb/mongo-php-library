<?php

namespace MongoDB\Tests\SpecTests;

use Closure;
use MongoDB\BSON\Binary;
use MongoDB\BSON\Int64;
use MongoDB\Client;
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
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Throwable;
use UnexpectedValueException;
use function base64_decode;
use function basename;
use function file_get_contents;
use function glob;
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
    use SetUpTearDownTrait;

    const LOCAL_MASTERKEY = 'Mng0NCt4ZHVUYUJCa1kxNkVyNUR1QURhZ2h2UzR2d2RrZzh0cFBwM3R6NmdWMDFBMUN3YkQ5aXRRMkhGRGdQV09wOGVNYUMxT2k3NjZKelhaQmRCZGJkTXVyZG9uSjFk';

    private function doSetUp()
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
    public static function assertCommandMatches(stdClass $expected, stdClass $actual)
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
    public function testClientSideEncryption(stdClass $test, array $runOn = null, array $data, array $keyVaultData = null, $jsonSchema = null, $databaseName = null, $collectionName = null)
    {
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
            $keyVaultData = $json->key_vault_data ?? null;
            $jsonSchema = $json->json_schema ?? null;
            $databaseName = $json->database_name ?? null;
            $collectionName = $json->collection_name ?? null;

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
    public function testDataKeyAndDoubleEncryption(Closure $test)
    {
        $client = new Client(static::getUri());

        $client->selectCollection('keyvault', 'datakeys')->drop();
        $client->selectCollection('db', 'coll')->drop();

        $encryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
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

        $clientEncrypted = new Client(static::getUri(), [], ['autoEncryption' => $autoEncryptionOpts]);
        $clientEncryption = $clientEncrypted->createClientEncryption($encryptionOpts);

        $test($clientEncryption, $client, $clientEncrypted, $this);
    }

    public static function dataKeyProvider()
    {
        return [
            'local' => [
                static function (ClientEncryption $clientEncryption, Client $client, Client $clientEncrypted, self $test) {
                    $commands = [];

                    $localDatakeyId = null;

                    (new CommandObserver())->observe(
                        function () use ($clientEncryption, &$localDatakeyId) {
                            $localDatakeyId = $clientEncryption->createDataKey('local', ['keyAltNames' => ['local_altname']]);
                        },
                        function ($command) use (&$commands) {
                            $commands[] = $command;
                        }
                    );

                    $test->assertInstanceOf(Binary::class, $localDatakeyId);
                    $test->assertSame(Binary::TYPE_UUID, $localDatakeyId->getType());

                    $test->assertCount(2, $commands);
                    $insert = $commands[1]['started'];
                    $test->assertSame('insert', $insert->getCommandName());
                    $test->assertSame(WriteConcern::MAJORITY, $insert->getCommand()->writeConcern->w);

                    $keys = $client->selectCollection('keyvault', 'datakeys')->find(['_id' => $localDatakeyId]);
                    $keys = iterator_to_array($keys);
                    $test->assertCount(1, $keys);

                    $key = $keys[0];
                    $test->assertNotNull($key);
                    $test->assertSame('local', $key['masterKey']['provider']);

                    $localEncrypted = $clientEncryption->encrypt('hello local', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $localDatakeyId]);
                    $test->assertInstanceOf(Binary::class, $localEncrypted);
                    $test->assertSame(Binary::TYPE_ENCRYPTED, $localEncrypted->getType());

                    $clientEncrypted->selectCollection('db', 'coll')->insertOne(['_id' => 'local', 'value' => $localEncrypted]);
                    $helloLocal = $clientEncrypted->selectCollection('db', 'coll')->findOne(['_id' => 'local']);
                    $test->assertNotNull($helloLocal);
                    $test->assertSame('hello local', $helloLocal['value']);

                    $localEncryptedAltName = $clientEncryption->encrypt('hello local', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyAltName' => 'local_altname']);
                    $test->assertEquals($localEncrypted, $localEncryptedAltName);

                    $test->expectException(BulkWriteException::class);
                    $clientEncrypted->selectCollection('db', 'coll')->insertOne(['encrypted_placeholder' => $localEncrypted]);
                },
            ],
            'aws' => [
                static function (ClientEncryption $clientEncryption, Client $client, Client $clientEncrypted, self $test) {
                    $commands = [];
                    $awsDatakeyId = null;

                    (new CommandObserver())->observe(
                        function () use ($clientEncryption, &$awsDatakeyId) {
                            $awsDatakeyId = $clientEncryption->createDataKey('aws', ['keyAltNames' => ['aws_altname'], 'masterKey' => ['region' => 'us-east-1', 'key' => 'arn:aws:kms:us-east-1:579766882180:key/89fcc2c4-08b0-4bd9-9f25-e30687b580d0']]);
                        },
                        function ($command) use (&$commands) {
                            $commands[] = $command;
                        }
                    );

                    $test->assertInstanceOf(Binary::class, $awsDatakeyId);
                    $test->assertSame(Binary::TYPE_UUID, $awsDatakeyId->getType());

                    $test->assertCount(2, $commands);
                    $insert = $commands[1]['started'];
                    $test->assertSame('insert', $insert->getCommandName());
                    $test->assertSame(WriteConcern::MAJORITY, $insert->getCommand()->writeConcern->w);

                    $keys = $client->selectCollection('keyvault', 'datakeys')->find(['_id' => $awsDatakeyId]);
                    $keys = iterator_to_array($keys);
                    $test->assertCount(1, $keys);

                    $key = $keys[0];
                    $test->assertNotNull($key);
                    $test->assertSame('aws', $key['masterKey']['provider']);

                    $awsEncrypted = $clientEncryption->encrypt('hello aws', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $awsDatakeyId]);
                    $test->assertInstanceOf(Binary::class, $awsEncrypted);
                    $test->assertSame(Binary::TYPE_ENCRYPTED, $awsEncrypted->getType());

                    $clientEncrypted->selectCollection('db', 'coll')->insertOne(['_id' => 'aws', 'value' => $awsEncrypted]);
                    $helloAws = $clientEncrypted->selectCollection('db', 'coll')->findOne(['_id' => 'aws']);
                    $test->assertNotNull($helloAws);
                    $test->assertSame('hello aws', $helloAws['value']);

                    $awsEncryptedAltName = $clientEncryption->encrypt('hello aws', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyAltName' => 'aws_altname']);
                    $test->assertEquals($awsEncrypted, $awsEncryptedAltName);

                    $test->expectException(BulkWriteException::class);
                    $clientEncrypted->selectCollection('db', 'coll')->insertOne(['encrypted_placeholder' => $awsEncrypted]);
                },
            ],
        ];
    }

    /**
     * Prose test: External Key Vault
     *
     * @testWith [false]
     *           [true]
     */
    public function testExternalKeyVault($withExternalKeyVault)
    {
        $client = new Client(static::getUri());

        $client->selectCollection('keyvault', 'datakeys')->drop();
        $client->selectCollection('db', 'coll')->drop();

        $keyId = $client
            ->selectCollection('keyvault', 'datakeys')
            ->insertOne($this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/external/external-key.json')))
            ->getInsertedId();

        $encryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
        ];

        if ($withExternalKeyVault) {
            $encryptionOpts['keyVaultClient'] = new Client(static::getUri(), ['username' => 'fake-user', 'password' => 'fake-pwd']);
        }

        $autoEncryptionOpts = $encryptionOpts + [
            'schemaMap' => [
                'db.coll' => $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/external/external-schema.json')),
            ],
        ];

        $clientEncrypted = new Client(static::getUri(), [], ['autoEncryption' => $autoEncryptionOpts]);
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

    /**
     * Prose test: BSON size limits and batch splitting
     */
    public function testBSONSizeLimitsAndBatchSplitting()
    {
        $client = new Client(static::getUri());

        $client->selectCollection('keyvault', 'datakeys')->drop();
        $client->selectCollection('db', 'coll')->drop();

        $client->selectDatabase('db')->createCollection('coll', ['validator' => ['$jsonSchema' => $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/limits/limits-schema.json'))]]);
        $client->selectCollection('keyvault', 'datakeys')->insertOne($this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/limits/limits-key.json')));

        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
            'keyVaultClient' => $client,
        ];

        $clientEncrypted = new Client(static::getUri(), [], ['autoEncryption' => $autoEncryptionOpts]);

        $collection = $clientEncrypted->selectCollection('db', 'coll');

        $document = json_decode(file_get_contents(__DIR__ . '/client-side-encryption/limits/limits-doc.json'), true);

        // Test 1
        $collection->insertOne(['_id' => 'over_2mib_under_16mib', 'unencrypted' => str_repeat('a', 2097152)]);

        // Test 2
        $collection->insertOne(
            ['_id' => 'encryption_exceeds_2mib', 'unencrypted' => str_repeat('a', 2097152 - 2000)] + $document
        );

        // Test 3
        $commands = [];
        (new CommandObserver())->observe(
            function () use ($collection) {
                $collection->insertMany([
                    ['_id' => 'over_2mib_1', 'unencrypted' => str_repeat('a', 2097152)],
                    ['_id' => 'over_2mib_2', 'unencrypted' => str_repeat('a', 2097152)],
                ]);
            },
            function ($command) use (&$commands) {
                $commands[] = $command;
            }
        );

        $this->assertCount(2, $commands);
        foreach ($commands as $command) {
            $this->assertSame('insert', $command['started']->getCommandName());
        }

        // Test 4
        $commands = [];
        (new CommandObserver())->observe(
            function () use ($collection, $document) {
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
            function ($command) use (&$commands) {
                $commands[] = $command;
            }
        );

        $this->assertCount(2, $commands);
        foreach ($commands as $command) {
            $this->assertSame('insert', $command['started']->getCommandName());
        }

        // Test 5
        $collection->insertOne(['_id' => 'under_16mib', 'unencrypted' => str_repeat('a', 16777216 - 2000)]);

        // Test 6
        $this->expectException(BulkWriteException::class);
        $this->expectExceptionMessageRegExp('#object to insert too large#');
        $collection->insertOne(['_id' => 'encryption_exceeds_16mib', 'unencrypted' => str_repeat('a', 16777216 - 2000)] + $document);
    }

    /**
     * Prose test: Views are prohibited
     */
    public function testViewsAreProhibited()
    {
        $client = new Client(static::getUri());

        $client->selectCollection('db', 'view')->drop();
        $client->selectDatabase('db')->command(['create' => 'view', 'viewOn' => 'coll']);

        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY), 0)],
            ],
        ];

        $clientEncrypted = new Client(static::getUri(), [], ['autoEncryption' => $autoEncryptionOpts]);

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
    public function testCorpus($schemaMap = true)
    {
        $client = new Client(static::getUri());

        $client->selectDatabase('db')->dropCollection('coll');

        $schema = $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-schema.json'));

        if (! $schemaMap) {
            $client
                ->selectDatabase('db')
                ->createCollection('coll', ['validator' => ['$jsonSchema' => $schema]]);
        }

        $client->selectDatabase('keyvault')->dropCollection('datakeys');
        $client->selectCollection('keyvault', 'datakeys')->insertMany([
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-local.json')),
            $this->decodeJson(file_get_contents(__DIR__ . '/client-side-encryption/corpus/corpus-key-aws.json')),
        ]);

        $encryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
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

        $clientEncrypted = new Client(static::getUri(), [], ['autoEncryption' => $autoEncryptionOpts]);
        $clientEncryption = $clientEncrypted->createClientEncryption($encryptionOpts);

        $collection = $clientEncrypted->selectCollection('db', 'coll');

        foreach ($corpus as $fieldName => $data) {
            switch ($fieldName) {
                case '_id':
                case 'altname_aws':
                case 'altname_local':
                    $corpusCopied[$fieldName] = $data;
                    break;

                default:
                    $corpusCopied[$fieldName] = $this->prepareCorpusData($data, $clientEncryption);
            }
        }

        $collection->insertOne($corpusCopied);
        $corpusDecrypted = $collection->findOne(['_id' => 'client_side_encryption_corpus']);

        $this->assertDocumentsMatch($corpus, $corpusDecrypted);
    }

    /**
     * Prose test: Custom Endpoint
     */
    public function testCustomEndpoint()
    {
        // Test 1
        $client = new Client(static::getUri());

        $encryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => [
                'aws' => Context::getAWSCredentials(),
            ],
        ];

        $clientEncryption = $client->createClientEncryption($encryptionOpts);

        // Test 2
        $masterKeyConfig = ['region' => 'us-east-1', 'key' => 'arn:aws:kms:us-east-1:579766882180:key/89fcc2c4-08b0-4bd9-9f25-e30687b580d0'];
        $keyId = $clientEncryption->createDataKey('aws', ['masterKey' => $masterKeyConfig]);
        $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
        $this->assertSame('test', $clientEncryption->decrypt($encrypted));

        // Test 3
        $keyId = $clientEncryption->createDataKey('aws', ['masterKey' => $masterKeyConfig + ['endpoint' => 'kms.us-east-1.amazonaws.com']]);
        $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
        $this->assertSame('test', $clientEncryption->decrypt($encrypted));

        // Test 4
        $keyId = $clientEncryption->createDataKey('aws', ['masterKey' => $masterKeyConfig + [ 'endpoint' => 'kms.us-east-1.amazonaws.com:443']]);
        $encrypted = $clientEncryption->encrypt('test', ['algorithm' => ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC, 'keyId' => $keyId]);
        $this->assertSame('test', $clientEncryption->decrypt($encrypted));

        // Test 5
        try {
            $clientEncryption->createDataKey('aws', ['masterKey' => $masterKeyConfig + [ 'endpoint' => 'kms.us-east-1.amazonaws.com:12345']]);
            $this->fail('Expected exception to be thrown');
        } catch (ConnectionException $e) {
        }

        // Test 6
        try {
            $clientEncryption->createDataKey('aws', ['masterKey' => $masterKeyConfig + [ 'endpoint' => 'kms.us-east-2.amazonaws.com']]);
            $this->fail('Expected exception to be thrown');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('us-east-1', $e->getMessage());
        }

        // Test 7
        try {
            $clientEncryption->createDataKey('aws', ['masterKey' => $masterKeyConfig + [ 'endpoint' => 'example.com']]);
            $this->fail('Expected exception to be thrown');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('parse error', $e->getMessage());
        }
    }

    /**
     * Prose test: Bypass spawning mongocryptd (via mongocryptdBypassSpawn)
     */
    public function testBypassSpawningMongocryptdViaBypassSpawn()
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

        $clientEncrypted = new Client(static::getUri(), [], ['autoEncryption' => $autoEncryptionOpts]);

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
    public function testBypassSpawningMongocryptdViaBypassAutoEncryption()
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

        $clientEncrypted = new Client(static::getUri(), [], ['autoEncryption' => $autoEncryptionOpts]);

        $clientEncrypted->selectCollection('db', 'coll')->insertOne(['encrypted' => 'test']);

        $clientMongocryptd = new Client('mongodb://localhost:27021');

        $this->expectException(ConnectionTimeoutException::class);
        $clientMongocryptd->selectDatabase('db')->command(['isMaster' => true]);
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

    private function createTestCollection($jsonSchema)
    {
        $options = empty($jsonSchema) ? [] : ['validator' => ['$jsonSchema' => $jsonSchema]];
        $operation = new CreateCollection($this->getContext()->databaseName, $this->getContext()->collectionName, $options);
        $operation->execute($this->getPrimaryServer());
    }

    private function encryptCorpusValue(stdClass $data, ClientEncryption $clientEncryption)
    {
        $encryptionOptions = [
            'algorithm' => $data->algo === 'rand' ? ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_RANDOM : ClientEncryption::AEAD_AES_256_CBC_HMAC_SHA_512_DETERMINISTIC,
        ];

        switch ($data->identifier) {
            case 'id':
                $keyId = $data->kms === 'local' ? 'LOCALAAAAAAAAAAAAAAAAA==' : 'AWSAAAAAAAAAAAAAAAAAAA==';
                $encryptionOptions['keyId'] = new Binary(base64_decode($keyId), 4);
                break;

            case 'altname':
                $encryptionOptions['keyAltName'] = $data->kms === 'local' ? 'local' : 'aws';
                break;

            default:
                throw new UnexpectedValueException('Unexpected value "%s" for identifier', $data->identifier);
        }

        if ($data->allowed) {
            $encrypted = $clientEncryption->encrypt($this->craftInt64($data), $encryptionOptions);
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

    private function insertKeyVaultData(array $keyVaultData = null)
    {
        if (empty($keyVaultData)) {
            return;
        }

        $context = $this->getContext();
        $collection = $context->selectCollection('keyvault', 'datakeys', ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)] + $context->defaultWriteOptions);
        $collection->drop();
        $collection->insertMany($keyVaultData);

        return;
    }

    private function prepareCorpusData(stdClass $data, ClientEncryption $clientEncryption)
    {
        if ($data->method === 'auto') {
            $data->value = $this->craftInt64($data);

            return $data;
        }

        $returnData = clone $data;
        $returnData->value = $this->encryptCorpusValue($data, $clientEncryption);

        return $data->allowed ? $returnData : $data;
    }
}
