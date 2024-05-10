<?php

namespace MongoDB\Tests\SpecTests;

use LogicException;
use MongoDB\Client;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use PHPUnit\Framework\Assert;
use stdClass;

use function array_diff_key;
use function array_keys;
use function getenv;
use function implode;
use function PHPUnit\Framework\assertLessThanOrEqual;
use function sprintf;

/**
 * Execution context for spec tests.
 *
 * This object tracks state that would be difficult to store on the test itself
 * due to the design of PHPUnit's data providers and setUp/tearDown methods.
 */
final class Context
{
    public ?string $bucketName = null;

    private ?Client $client = null;

    public ?string $collectionName = null;

    public string $databaseName;

    public array $defaultWriteOptions = [];

    public array $outcomeReadOptions = [];

    public ?string $outcomeCollectionName = null;

    public ?Session $session0 = null;

    public object $session0Lsid;

    public ?Session $session1 = null;

    public object $session1Lsid;

    public bool $useEncryptedClientIfConfigured = false;

    private Client $internalClient;

    private ?Client $encryptedClient = null;

    private function __construct(string $databaseName, ?string $collectionName)
    {
        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->outcomeCollectionName = $collectionName;
        $this->internalClient = FunctionalTestCase::createTestClient();
    }

    public static function fromClientSideEncryption(stdClass $test, $databaseName, $collectionName)
    {
        $o = new self($databaseName, $collectionName);

        $clientOptions = isset($test->clientOptions) ? (array) $test->clientOptions : [];

        $autoEncryptionOptions = [];

        if (isset($clientOptions['autoEncryptOpts'])) {
            $autoEncryptionOptions = (array) $clientOptions['autoEncryptOpts'] + ['keyVaultNamespace' => 'keyvault.datakeys'];
            unset($clientOptions['autoEncryptOpts']);

            // Ensure test doesn't specify conflicting options for AWS
            $countAws = (isset($autoEncryptionOptions['kmsProviders']->aws) ? 1 : 0);
            $countAws += (isset($autoEncryptionOptions['kmsProviders']->awsTemporary) ? 1 : 0);
            $countAws += (isset($autoEncryptionOptions['kmsProviders']->awsTemporaryNoSessionToken) ? 1 : 0);
            assertLessThanOrEqual(1, $countAws, 'aws, awsTemporary, and awsTemporaryNoSessionToken are mutually exclusive');

            if (isset($autoEncryptionOptions['kmsProviders']->aws)) {
                $autoEncryptionOptions['kmsProviders']->aws = self::getAWSCredentials();
            }

            if (isset($autoEncryptionOptions['kmsProviders']->awsTemporary)) {
                unset($autoEncryptionOptions['kmsProviders']->awsTemporary);
                $autoEncryptionOptions['kmsProviders']->aws = self::getAWSTempCredentials(true);
            }

            if (isset($autoEncryptionOptions['kmsProviders']->awsTemporaryNoSessionToken)) {
                unset($autoEncryptionOptions['kmsProviders']->awsTemporaryNoSessionToken);
                $autoEncryptionOptions['kmsProviders']->aws = self::getAWSTempCredentials(false);
            }

            if (isset($autoEncryptionOptions['kmsProviders']->azure)) {
                $autoEncryptionOptions['kmsProviders']->azure = self::getAzureCredentials();
            }

            if (isset($autoEncryptionOptions['kmsProviders']->gcp)) {
                $autoEncryptionOptions['kmsProviders']->gcp = self::getGCPCredentials();
            }

            if (isset($autoEncryptionOptions['kmsProviders']->kmip)) {
                $autoEncryptionOptions['kmsProviders']->kmip = ['endpoint' => self::getKmipEndpoint()];

                if (empty($autoEncryptionOptions['tlsOptions'])) {
                    $autoEncryptionOptions['tlsOptions'] = new stdClass();
                }

                $autoEncryptionOptions['tlsOptions']->kmip = self::getKmsTlsOptions();
            }

            // Intentionally ignore empty values for CRYPT_SHARED_LIB_PATH
            if (getenv('CRYPT_SHARED_LIB_PATH')) {
                $autoEncryptionOptions['extraOptions']['cryptSharedLibPath'] = getenv('CRYPT_SHARED_LIB_PATH');
            }
        }

        if (isset($test->outcome->collection->name)) {
            $o->outcomeCollectionName = $test->outcome->collection->name;
        }

        $o->defaultWriteOptions = ['writeConcern' => new WriteConcern(WriteConcern::MAJORITY)];

        $o->client = self::createTestClient(null, $clientOptions);

        if ($autoEncryptionOptions !== []) {
            $o->encryptedClient = self::createTestClient(null, $clientOptions, ['autoEncryption' => $autoEncryptionOptions]);
        }

        return $o;
    }

    public static function fromCrud(stdClass $test, $databaseName, $collectionName)
    {
        $o = new self($databaseName, $collectionName);

        $clientOptions = isset($test->clientOptions) ? (array) $test->clientOptions : [];

        if (isset($test->outcome->collection->name)) {
            $o->outcomeCollectionName = $test->outcome->collection->name;
        }

        $o->defaultWriteOptions = [
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $o->outcomeReadOptions = [
            'readConcern' => new ReadConcern('local'),
            'readPreference' => new ReadPreference(ReadPreference::PRIMARY),
        ];

        $o->client = self::createTestClient(null, $clientOptions);

        return $o;
    }

    public static function fromReadWriteConcern(stdClass $test, $databaseName, $collectionName)
    {
        $o = new self($databaseName, $collectionName);

        if (isset($test->outcome->collection->name)) {
            $o->outcomeCollectionName = $test->outcome->collection->name;
        }

        $clientOptions = isset($test->clientOptions) ? (array) $test->clientOptions : [];

        $o->client = self::createTestClient(null, $clientOptions);

        return $o;
    }

    public static function fromRetryableReads(stdClass $test, $databaseName, $collectionName, $bucketName)
    {
        $o = new self($databaseName, $collectionName);

        $o->bucketName = $bucketName;

        $clientOptions = isset($test->clientOptions) ? (array) $test->clientOptions : [];

        $o->client = self::createTestClient(null, $clientOptions);

        return $o;
    }

    public static function fromTransactions(stdClass $test, $databaseName, $collectionName, $useMultipleMongoses)
    {
        $o = new self($databaseName, $collectionName);

        $o->defaultWriteOptions = [
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $o->outcomeReadOptions = [
            'readConcern' => new ReadConcern('local'),
            'readPreference' => new ReadPreference(ReadPreference::PRIMARY),
        ];

        $clientOptions = isset($test->clientOptions) ? (array) $test->clientOptions : [];

        $o->client = self::createTestClient(FunctionalTestCase::getUri($useMultipleMongoses), $clientOptions);

        $session0Options = isset($test->sessionOptions->session0) ? (array) $test->sessionOptions->session0 : [];
        $session1Options = isset($test->sessionOptions->session1) ? (array) $test->sessionOptions->session1 : [];

        $o->session0 = $o->client->startSession($o->prepareSessionOptions($session0Options));
        $o->session1 = $o->client->startSession($o->prepareSessionOptions($session1Options));

        $o->session0Lsid = $o->session0->getLogicalSessionId();
        $o->session1Lsid = $o->session1->getLogicalSessionId();

        return $o;
    }

    public static function getAWSCredentials(): array
    {
        return [
            'accessKeyId' => static::getEnv('AWS_ACCESS_KEY_ID'),
            'secretAccessKey' => static::getEnv('AWS_SECRET_ACCESS_KEY'),
        ];
    }

    public static function getAWSTempCredentials(bool $withSessionToken): array
    {
        $awsTempCredentials = [
            'accessKeyId' => static::getEnv('AWS_TEMP_ACCESS_KEY_ID'),
            'secretAccessKey' => static::getEnv('AWS_TEMP_SECRET_ACCESS_KEY'),
        ];

        if ($withSessionToken) {
            $awsTempCredentials['sessionToken'] = static::getEnv('AWS_TEMP_SESSION_TOKEN');
        }

        return $awsTempCredentials;
    }

    public static function getAzureCredentials(): array
    {
        return [
            'tenantId' => static::getEnv('AZURE_TENANT_ID'),
            'clientId' => static::getEnv('AZURE_CLIENT_ID'),
            'clientSecret' => static::getEnv('AZURE_CLIENT_SECRET'),
        ];
    }

    public static function getKmipEndpoint(): string
    {
        return static::getEnv('KMIP_ENDPOINT');
    }

    public static function getKmsTlsOptions(): array
    {
        return [
            'tlsCAFile' => static::getEnv('KMS_TLS_CA_FILE'),
            'tlsCertificateKeyFile' => static::getEnv('KMS_TLS_CERTIFICATE_KEY_FILE'),
        ];
    }

    public static function getGCPCredentials(): array
    {
        return [
            'email' => static::getEnv('GCP_EMAIL'),
            'privateKey' => static::getEnv('GCP_PRIVATE_KEY'),
        ];
    }

    public function getClient(): Client
    {
        return $this->useEncryptedClientIfConfigured && $this->encryptedClient ? $this->encryptedClient : $this->client;
    }

    public function getCollection(array $collectionOptions = [], array $databaseOptions = [])
    {
        return $this->selectCollection(
            $this->databaseName,
            $this->collectionName,
            $collectionOptions,
            $databaseOptions,
        );
    }

    public function getDatabase(array $databaseOptions = [])
    {
        return $this->selectDatabase($this->databaseName, $databaseOptions);
    }

    public function getGridFSBucket(array $bucketOptions = [])
    {
        return $this->selectGridFSBucket($this->databaseName, $this->bucketName, $bucketOptions);
    }

    public function getInternalClient(): Client
    {
        return $this->internalClient;
    }

    /**
     * Prepare options readConcern, readPreference, and writeConcern options by
     * creating value objects.
     *
     * @throws LogicException if any option keys are unsupported
     */
    public function prepareOptions(array $options): array
    {
        if (isset($options['readConcern']) && ! ($options['readConcern'] instanceof ReadConcern)) {
            $readConcern = (array) $options['readConcern'];
            $diff = array_diff_key($readConcern, ['level' => 1]);

            if (! empty($diff)) {
                throw new LogicException('Unsupported readConcern args: ' . implode(',', array_keys($diff)));
            }

            $options['readConcern'] = new ReadConcern($readConcern['level']);
        }

        if (isset($options['readPreference']) && ! ($options['readPreference'] instanceof ReadPreference)) {
            $readPreference = (array) $options['readPreference'];
            $diff = array_diff_key($readPreference, ['mode' => 1]);

            if (! empty($diff)) {
                throw new LogicException('Unsupported readPreference args: ' . implode(',', array_keys($diff)));
            }

            $options['readPreference'] = new ReadPreference($readPreference['mode']);
        }

        if (isset($options['writeConcern']) && ! ($options['writeConcern'] instanceof WriteConcern)) {
            $writeConcern = (array) $options['writeConcern'];
            $diff = array_diff_key($writeConcern, ['w' => 1, 'wtimeout' => 1, 'j' => 1]);

            if (! empty($diff)) {
                throw new LogicException('Unsupported writeConcern args: ' . implode(',', array_keys($diff)));
            }

            if (! empty($writeConcern)) {
                $w = $writeConcern['w'];
                $wtimeout = $writeConcern['wtimeout'] ?? 0;
                $j = $writeConcern['j'] ?? null;

                $options['writeConcern'] = isset($j)
                    ? new WriteConcern($w, $wtimeout, $j)
                    : new WriteConcern($w, $wtimeout);
            } else {
                unset($options['writeConcern']);
            }
        }

        return $options;
    }

    /**
     * Replace a session placeholder in an operation arguments array.
     *
     * Note: this method will modify the $args parameter.
     *
     * @param array $args Operation arguments
     * @throws LogicException if the session placeholder is unsupported
     */
    public function replaceArgumentSessionPlaceholder(array &$args): void
    {
        if (! isset($args['session'])) {
            return;
        }

        switch ($args['session']) {
            case 'session0':
                $args['session'] = $this->session0;
                break;

            case 'session1':
                $args['session'] = $this->session1;
                break;

            default:
                throw new LogicException('Unsupported session placeholder: ' . $args['session']);
        }
    }

    /**
     * Replace a logical session ID placeholder in a command document.
     *
     * Note: this method will modify the $command parameter.
     *
     * @param stdClass $command Command document
     * @throws LogicException if the session placeholder is unsupported
     */
    public function replaceCommandSessionPlaceholder(stdClass $command): void
    {
        if (! isset($command->lsid)) {
            return;
        }

        switch ($command->lsid) {
            case 'session0':
                $command->lsid = $this->session0Lsid;
                break;

            case 'session1':
                $command->lsid = $this->session1Lsid;
                break;

            default:
                throw new LogicException('Unsupported session placeholder: ' . $command->lsid);
        }
    }

    public function selectCollection($databaseName, $collectionName, array $collectionOptions = [], array $databaseOptions = [])
    {
        return $this
            ->selectDatabase($databaseName, $databaseOptions)
            ->selectCollection($collectionName, $this->prepareOptions($collectionOptions));
    }

    public function selectDatabase($databaseName, array $databaseOptions = [])
    {
        return $this->getClient()->selectDatabase(
            $databaseName,
            $this->prepareOptions($databaseOptions),
        );
    }

    public function selectGridFSBucket($databaseName, $bucketName, array $bucketOptions = [])
    {
        return $this->selectDatabase($databaseName)->selectGridFSBucket($this->prepareGridFSBucketOptions($bucketOptions, $bucketName));
    }

    private static function createTestClient(?string $uri = null, array $options = [], array $driverOptions = []): Client
    {
        /* Default to using a dedicated client. This was already necessary for
         * CSFLE and Transaction spec tests, but is generally useful for any
         * test that observes command monitoring events. */
        $driverOptions += ['disableClientPersistence' => true];

        return FunctionalTestCase::createTestClient($uri, $options, $driverOptions);
    }

    private static function getEnv(string $name): string
    {
        $value = getenv($name);

        if ($value === false) {
            Assert::markTestSkipped(sprintf('Environment variable "%s" is not defined', $name));
        }

        return $value;
    }

    private function prepareGridFSBucketOptions(array $options, $bucketPrefix)
    {
        if ($bucketPrefix !== null) {
            $options['bucketPrefix'] = $bucketPrefix;
        }

        return $options;
    }

    private function prepareSessionOptions(array $options)
    {
        if (isset($options['defaultTransactionOptions'])) {
            $options['defaultTransactionOptions'] = $this->prepareOptions((array) $options['defaultTransactionOptions']);
        }

        return $options;
    }
}
