<?php
/*
 * Copyright 2015-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB;

use Composer\InstalledVersions;
use Iterator;
use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Builder\Pipeline;
use MongoDB\Codec\Encoder;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\InvalidArgumentException as DriverInvalidArgumentException;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Monitoring\Subscriber;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\DatabaseInfo;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\ListDatabaseNames;
use MongoDB\Operation\ListDatabases;
use MongoDB\Operation\Watch;
use stdClass;
use Throwable;

use function array_diff_key;
use function is_array;
use function is_string;
use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

class Client
{
    public const DEFAULT_URI = 'mongodb://127.0.0.1/';

    private const DEFAULT_TYPE_MAP = [
        'array' => BSONArray::class,
        'document' => BSONDocument::class,
        'root' => BSONDocument::class,
    ];

    private const HANDSHAKE_SEPARATOR = '/';

    private static ?string $version = null;

    private Manager $manager;

    private ReadConcern $readConcern;

    private ReadPreference $readPreference;

    private string $uri;

    private array $typeMap;

    /** @psalm-var Encoder<array|stdClass|Document|PackedArray, mixed> */
    private readonly Encoder $builderEncoder;

    private WriteConcern $writeConcern;

    /**
     * Constructs a new Client instance.
     *
     * This is the preferred class for connecting to a MongoDB server or
     * cluster of servers. It serves as a gateway for accessing individual
     * databases and collections.
     *
     * Supported driver-specific options:
     *
     *  * builderEncoder (MongoDB\Builder\Encoder): Encoder for query and
     *    aggregation builders. If not given, the default encoder will be used.
     *
     *  * typeMap (array): Default type map for cursors and BSON documents.
     *
     * Other options are documented in MongoDB\Driver\Manager::__construct().
     *
     * @see https://mongodb.com/docs/manual/reference/connection-string/
     * @see https://php.net/manual/en/mongodb-driver-manager.construct.php
     * @see https://php.net/manual/en/mongodb.persistence.php#mongodb.persistence.typemaps
     * @param string|null $uri           MongoDB connection string. If none is provided, this defaults to self::DEFAULT_URI.
     * @param array       $uriOptions    Additional connection string options
     * @param array       $driverOptions Driver-specific options
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverInvalidArgumentException for parameter/option parsing errors in the driver
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function __construct(?string $uri = null, array $uriOptions = [], array $driverOptions = [])
    {
        $driverOptions += ['typeMap' => self::DEFAULT_TYPE_MAP];

        if (! is_array($driverOptions['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" driver option', $driverOptions['typeMap'], 'array');
        }

        if (isset($driverOptions['autoEncryption']['keyVaultClient'])) {
            if ($driverOptions['autoEncryption']['keyVaultClient'] instanceof self) {
                $driverOptions['autoEncryption']['keyVaultClient'] = $driverOptions['autoEncryption']['keyVaultClient']->manager;
            } elseif (! $driverOptions['autoEncryption']['keyVaultClient'] instanceof Manager) {
                throw InvalidArgumentException::invalidType('"keyVaultClient" autoEncryption option', $driverOptions['autoEncryption']['keyVaultClient'], [self::class, Manager::class]);
            }
        }

        if (isset($driverOptions['builderEncoder']) && ! $driverOptions['builderEncoder'] instanceof Encoder) {
            throw InvalidArgumentException::invalidType('"builderEncoder" option', $driverOptions['builderEncoder'], Encoder::class);
        }

        $driverOptions['driver'] = $this->mergeDriverInfo($driverOptions['driver'] ?? []);

        $this->uri = $uri ?? self::DEFAULT_URI;
        $this->builderEncoder = $driverOptions['builderEncoder'] ?? new BuilderEncoder();
        $this->typeMap = $driverOptions['typeMap'];

        $driverOptions = array_diff_key($driverOptions, ['builderEncoder' => 1, 'typeMap' => 1]);

        $this->manager = new Manager($uri, $uriOptions, $driverOptions);
        $this->readConcern = $this->manager->getReadConcern();
        $this->readPreference = $this->manager->getReadPreference();
        $this->writeConcern = $this->manager->getWriteConcern();
    }

    /**
     * Return internal properties for debugging purposes.
     *
     * @see https://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'manager' => $this->manager,
            'uri' => $this->uri,
            'typeMap' => $this->typeMap,
            'builderEncoder' => $this->builderEncoder,
            'writeConcern' => $this->writeConcern,
        ];
    }

    /**
     * Select a database.
     *
     * Note: databases whose names contain special characters (e.g. "-") may
     * be selected with complex syntax (e.g. $client->{"that-database"}) or
     * {@link selectDatabase()}.
     *
     * @see https://php.net/oop5.overloading#object.get
     * @see https://php.net/types.string#language.types.string.parsing.complex
     * @param string $databaseName Name of the database to select
     * @return Database
     */
    public function __get(string $databaseName)
    {
        return $this->getDatabase($databaseName);
    }

    /**
     * Return the connection string (i.e. URI).
     *
     * @return string
     */
    public function __toString()
    {
        return $this->uri;
    }

    /**
     * Registers a monitoring event subscriber with this Client's Manager
     *
     * @see Manager::addSubscriber()
     */
    final public function addSubscriber(Subscriber $subscriber): void
    {
        $this->manager->addSubscriber($subscriber);
    }

    /**
     * Returns a ClientEncryption instance for explicit encryption and decryption
     *
     * @param array $options Encryption options
     *
     * @return ClientEncryption
     */
    public function createClientEncryption(array $options)
    {
        if (isset($options['keyVaultClient'])) {
            if ($options['keyVaultClient'] instanceof self) {
                $options['keyVaultClient'] = $options['keyVaultClient']->manager;
            } elseif (! $options['keyVaultClient'] instanceof Manager) {
                throw InvalidArgumentException::invalidType('"keyVaultClient" option', $options['keyVaultClient'], [self::class, Manager::class]);
            }
        }

        return $this->manager->createClientEncryption($options);
    }

    /**
     * Drop a database.
     *
     * @see DropDatabase::__construct() for supported options
     * @param string $databaseName Database name
     * @param array  $options      Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are unsupported on the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function dropDatabase(string $databaseName, array $options = [])
    {
        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        } else {
            @trigger_error(sprintf('The function %s() will return nothing in mongodb/mongodb v2.0, the "typeMap" option is deprecated', __FUNCTION__), E_USER_DEPRECATED);
        }

        $server = select_server_for_write($this->manager, $options);

        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new DropDatabase($databaseName, $options);

        return $operation->execute($server);
    }

    /**
     * Returns a collection instance.
     *
     * If the collection does not exist in the database, it is not created when
     * invoking this method.
     *
     * @see Collection::__construct() for supported options
     * @param string $databaseName   Name of the database containing the collection
     * @param string $collectionName Name of the collection to select
     * @param array  $options        Collection constructor options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function getCollection(string $databaseName, string $collectionName, array $options = []): Collection
    {
        $options += ['typeMap' => $this->typeMap, 'builderEncoder' => $this->builderEncoder];

        return new Collection($this->manager, $databaseName, $collectionName, $options);
    }

    /**
     * Returns a database instance.
     *
     * If the database does not exist on the server, it is not created when
     * invoking this method.
     *
     * @param string $databaseName Name of the database to select
     * @param array  $options      Database constructor options
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @see Database::__construct() for supported options
     */
    public function getDatabase(string $databaseName, array $options = []): Database
    {
        $options += ['typeMap' => $this->typeMap, 'builderEncoder' => $this->builderEncoder];

        return new Database($this->manager, $databaseName, $options);
    }

    /**
     * Return the Manager.
     *
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Return the read concern for this client.
     *
     * @see https://php.net/manual/en/mongodb-driver-readconcern.isdefault.php
     * @return ReadConcern
     */
    public function getReadConcern()
    {
        return $this->readConcern;
    }

    /**
     * Return the read preference for this client.
     *
     * @return ReadPreference
     */
    public function getReadPreference()
    {
        return $this->readPreference;
    }

    /**
     * Return the type map for this client.
     *
     * @return array
     */
    public function getTypeMap()
    {
        return $this->typeMap;
    }

    /**
     * Return the write concern for this client.
     *
     * @see https://php.net/manual/en/mongodb-driver-writeconcern.isdefault.php
     * @return WriteConcern
     */
    public function getWriteConcern()
    {
        return $this->writeConcern;
    }

    /**
     * List database names.
     *
     * @see ListDatabaseNames::__construct() for supported options
     * @throws UnexpectedValueException if the command response was malformed
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function listDatabaseNames(array $options = []): Iterator
    {
        $operation = new ListDatabaseNames($options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * List databases.
     *
     * @see ListDatabases::__construct() for supported options
     * @return Iterator<int, DatabaseInfo>
     * @throws UnexpectedValueException if the command response was malformed
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function listDatabases(array $options = [])
    {
        $operation = new ListDatabases($options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * Unregisters a monitoring event subscriber with this Client's Manager
     *
     * @see Manager::removeSubscriber()
     */
    final public function removeSubscriber(Subscriber $subscriber): void
    {
        $this->manager->removeSubscriber($subscriber);
    }

    /**
     * Select a collection.
     *
     * @see Collection::__construct() for supported options
     * @param string $databaseName   Name of the database containing the collection
     * @param string $collectionName Name of the collection to select
     * @param array  $options        Collection constructor options
     * @return Collection
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function selectCollection(string $databaseName, string $collectionName, array $options = [])
    {
        return $this->getCollection($databaseName, $collectionName, $options);
    }

    /**
     * Select a database.
     *
     * @see Database::__construct() for supported options
     * @param string $databaseName Name of the database to select
     * @param array  $options      Database constructor options
     * @return Database
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function selectDatabase(string $databaseName, array $options = [])
    {
        return $this->getDatabase($databaseName, $options);
    }

    /**
     * Start a new client session.
     *
     * @see https://php.net/manual/en/mongodb-driver-manager.startsession.php
     * @param array $options Session options
     * @return Session
     */
    public function startSession(array $options = [])
    {
        return $this->manager->startSession($options);
    }

    /**
     * Create a change stream for watching changes to the cluster.
     *
     * @see Watch::__construct() for supported options
     * @param array $pipeline Aggregation pipeline
     * @param array $options  Command options
     * @return ChangeStream
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function watch(array $pipeline = [], array $options = [])
    {
        if (is_builder_pipeline($pipeline)) {
            $pipeline = new Pipeline(...$pipeline);
        }

        $pipeline = $this->builderEncoder->encodeIfSupported($pipeline);

        if (! isset($options['readPreference']) && ! is_in_transaction($options)) {
            $options['readPreference'] = $this->readPreference;
        }

        $server = select_server($this->manager, $options);

        if (! isset($options['readConcern']) && ! is_in_transaction($options)) {
            $options['readConcern'] = $this->readConcern;
        }

        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $operation = new Watch($this->manager, null, null, $pipeline, $options);

        return $operation->execute($server);
    }

    private static function getVersion(): string
    {
        if (self::$version === null) {
            try {
                self::$version = InstalledVersions::getPrettyVersion('mongodb/mongodb') ?? 'unknown';
            } catch (Throwable) {
                self::$version = 'error';
            }
        }

        return self::$version;
    }

    private function mergeDriverInfo(array $driver): array
    {
        $mergedDriver = [
            'name' => 'PHPLIB',
            'version' => self::getVersion(),
        ];

        if (isset($driver['name'])) {
            if (! is_string($driver['name'])) {
                throw InvalidArgumentException::invalidType('"name" handshake option', $driver['name'], 'string');
            }

            $mergedDriver['name'] .= self::HANDSHAKE_SEPARATOR . $driver['name'];
        }

        if (isset($driver['version'])) {
            if (! is_string($driver['version'])) {
                throw InvalidArgumentException::invalidType('"version" handshake option', $driver['version'], 'string');
            }

            $mergedDriver['version'] .= self::HANDSHAKE_SEPARATOR . $driver['version'];
        }

        if (isset($driver['platform'])) {
            $mergedDriver['platform'] = $driver['platform'];
        }

        return $mergedDriver;
    }
}
