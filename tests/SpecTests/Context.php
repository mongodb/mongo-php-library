<?php

namespace MongoDB\Tests\SpecTests;

use LogicException;
use MongoDB\Client;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use stdClass;
use function array_diff_key;
use function array_keys;
use function implode;
use function mt_rand;

/**
 * Execution context for spec tests.
 *
 * This object tracks state that would be difficult to store on the test itself
 * due to the design of PHPUnit's data providers and setUp/tearDown methods.
 */
final class Context
{
    /** @var string|null */
    public $bucketName;

    /** @var Client|null */
    public $client;

    /** @var string */
    public $collectionName;

    /** @var string */
    public $databaseName;

    /** @var array */
    public $defaultWriteOptions = [];

    /** @var array */
    public $outcomeFindOptions = [];

    /** @var string */
    public $outcomeCollectionName;

    /** @var Session|null */
    public $session0;

    /** @var object */
    public $session0Lsid;

    /** @var Session|null */
    public $session1;

    /** @var object */
    public $session1Lsid;

    /**
     * @param string $databaseName
     * @param string $collectionName
     */
    private function __construct($databaseName, $collectionName)
    {
        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->outcomeCollectionName = $collectionName;
    }

    public static function fromChangeStreams(stdClass $test, $databaseName, $collectionName)
    {
        $o = new self($databaseName, $collectionName);

        $o->client = new Client(FunctionalTestCase::getUri());

        return $o;
    }

    public static function fromCommandMonitoring(stdClass $test, $databaseName, $collectionName)
    {
        $o = new self($databaseName, $collectionName);

        $o->client = new Client(FunctionalTestCase::getUri());

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

        $o->outcomeFindOptions = [
            'readConcern' => new ReadConcern('local'),
            'readPreference' => new ReadPreference('primary'),
        ];

        $o->client = new Client(FunctionalTestCase::getUri(), $clientOptions);

        return $o;
    }

    public static function fromRetryableReads(stdClass $test, $databaseName, $collectionName, $bucketName)
    {
        $o = new self($databaseName, $collectionName);

        $o->bucketName = $bucketName;

        $clientOptions = isset($test->clientOptions) ? (array) $test->clientOptions : [];

        $o->client = new Client(FunctionalTestCase::getUri(), $clientOptions);

        return $o;
    }

    public static function fromRetryableWrites(stdClass $test, $databaseName, $collectionName, $useMultipleMongoses)
    {
        $o = new self($databaseName, $collectionName);

        $clientOptions = isset($test->clientOptions) ? (array) $test->clientOptions : [];

        // TODO: Remove this once retryWrites=true by default (see: PHPC-1324)
        $clientOptions['retryWrites'] = true;

        if (isset($test->outcome->collection->name)) {
            $o->outcomeCollectionName = $test->outcome->collection->name;
        }

        $o->client = new Client(FunctionalTestCase::getUri($useMultipleMongoses), $clientOptions);

        return $o;
    }

    public static function fromTransactions(stdClass $test, $databaseName, $collectionName, $useMultipleMongoses)
    {
        $o = new self($databaseName, $collectionName);

        $o->defaultWriteOptions = [
            'writeConcern' => new WriteConcern(WriteConcern::MAJORITY),
        ];

        $o->outcomeFindOptions = [
            'readConcern' => new ReadConcern('local'),
            'readPreference' => new ReadPreference('primary'),
        ];

        $clientOptions = isset($test->clientOptions) ? (array) $test->clientOptions : [];

        /* Transaction spec tests expect a new client for each test so that
         * txnNumber values are deterministic. Append a random option to avoid
         * re-using a previously persisted libmongoc client object. */
        $clientOptions += ['p' => mt_rand()];

        $o->client = new Client(FunctionalTestCase::getUri($useMultipleMongoses), $clientOptions);

        $session0Options = isset($test->sessionOptions->session0) ? (array) $test->sessionOptions->session0 : [];
        $session1Options = isset($test->sessionOptions->session1) ? (array) $test->sessionOptions->session1 : [];

        $o->session0 = $o->client->startSession($o->prepareSessionOptions($session0Options));
        $o->session1 = $o->client->startSession($o->prepareSessionOptions($session1Options));

        $o->session0Lsid = $o->session0->getLogicalSessionId();
        $o->session1Lsid = $o->session1->getLogicalSessionId();

        return $o;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function getCollection(array $collectionOptions = [])
    {
        return $this->selectCollection(
            $this->databaseName,
            $this->collectionName,
            $this->prepareOptions($collectionOptions)
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

    /**
     * Prepare options readConcern, readPreference, and writeConcern options by
     * creating value objects.
     *
     * @param array $options
     * @return array
     * @throws LogicException if any option keys are unsupported
     */
    public function prepareOptions(array $options)
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

            $w = $writeConcern['w'];
            $wtimeout = isset($writeConcern['wtimeout']) ? $writeConcern['wtimeout'] : 0;
            $j = isset($writeConcern['j']) ? $writeConcern['j'] : null;

            $options['writeConcern'] = isset($j)
                ? new WriteConcern($w, $wtimeout, $j)
                : new WriteConcern($w, $wtimeout);
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
    public function replaceArgumentSessionPlaceholder(array &$args)
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
    public function replaceCommandSessionPlaceholder(stdClass $command)
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

    public function selectCollection($databaseName, $collectionName, array $collectionOptions = [])
    {
        return $this->client->selectCollection(
            $databaseName,
            $collectionName,
            $this->prepareOptions($collectionOptions)
        );
    }

    public function selectDatabase($databaseName, array $databaseOptions = [])
    {
        return $this->client->selectDatabase(
            $databaseName,
            $this->prepareOptions($databaseOptions)
        );
    }

    public function selectGridFSBucket($databaseName, $bucketName, array $bucketOptions = [])
    {
        return $this->selectDatabase($databaseName)->selectGridFSBucket($this->prepareGridFSBucketOptions($bucketOptions, $bucketName));
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
