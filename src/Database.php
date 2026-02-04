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

use Iterator;
use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Builder\Pipeline;
use MongoDB\Codec\Encoder;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\CursorInterface;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\CreateEncryptedCollectionException;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\GridFS\Bucket;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\CollectionInfo;
use MongoDB\Operation\Aggregate;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\CreateEncryptedCollection;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\DropDatabase;
use MongoDB\Operation\DropEncryptedCollection;
use MongoDB\Operation\ListCollectionNames;
use MongoDB\Operation\ListCollections;
use MongoDB\Operation\ModifyCollection;
use MongoDB\Operation\RenameCollection;
use MongoDB\Operation\Watch;
use stdClass;
use Stringable;
use Throwable;

use function is_array;
use function is_bool;
use function strlen;

/** @psalm-no-seal-properties */
class Database implements Stringable
{
    private const DEFAULT_TYPE_MAP = [
        'array' => BSONArray::class,
        'document' => BSONDocument::class,
        'root' => BSONDocument::class,
    ];

    /** @psalm-var Encoder<array|stdClass|Document|PackedArray, mixed> */
    private readonly Encoder $builderEncoder;

    private ReadConcern $readConcern;

    private ReadPreference $readPreference;

    private array $typeMap;

    private WriteConcern $writeConcern;

    private bool $autoEncryptionEnabled;

    /**
     * Constructs new Database instance.
     *
     * This class provides methods for database-specific operations and serves
     * as a gateway for accessing collections.
     *
     * Supported options:
     *
     *  * builderEncoder (MongoDB\Codec\Encoder): Encoder for query and
     *    aggregation builders. If not given, the default encoder will be used.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): The default read concern to
     *    use for database operations and selected collections. Defaults to the
     *    Manager's read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The default read
     *    preference to use for database operations and selected collections.
     *    Defaults to the Manager's read preference.
     *
     *  * typeMap (array): Default type map for cursors and BSON documents.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): The default write concern
     *    to use for database operations and selected collections. Defaults to
     *    the Manager's write concern.
     *
     * @param Manager $manager      Manager instance from the driver
     * @param string  $databaseName Database name
     * @param array   $options      Database options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(private Manager $manager, private string $databaseName, array $options = [])
    {
        if (strlen($databaseName) < 1) {
            throw new InvalidArgumentException('$databaseName is invalid: ' . $databaseName);
        }

        if (isset($options['builderEncoder']) && ! $options['builderEncoder'] instanceof Encoder) {
            throw InvalidArgumentException::invalidType('"builderEncoder" option', $options['builderEncoder'], Encoder::class);
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], ReadConcern::class);
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], ReadPreference::class);
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], WriteConcern::class);
        }

        if (isset($options['autoEncryptionEnabled']) && ! is_bool($options['autoEncryptionEnabled'])) {
            throw InvalidArgumentException::invalidType('"autoEncryptionEnabled" option', $options['autoEncryptionEnabled'], 'boolean');
        }

        $this->builderEncoder = $options['builderEncoder'] ?? new BuilderEncoder();
        $this->readConcern = $options['readConcern'] ?? $this->manager->getReadConcern();
        $this->readPreference = $options['readPreference'] ?? $this->manager->getReadPreference();
        $this->typeMap = $options['typeMap'] ?? self::DEFAULT_TYPE_MAP;
        $this->writeConcern = $options['writeConcern'] ?? $this->manager->getWriteConcern();
        $this->autoEncryptionEnabled = $options['autoEncryptionEnabled'] ?? false;
    }

    /**
     * Return internal properties for debugging purposes.
     *
     * @see https://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     */
    public function __debugInfo(): array
    {
        return [
            'builderEncoder' => $this->builderEncoder,
            'databaseName' => $this->databaseName,
            'manager' => $this->manager,
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];
    }

    /**
     * Select a collection within this database.
     *
     * Note: collections whose names contain special characters (e.g. ".") may
     * be selected with complex syntax (e.g. $database->{"system.profile"}) or
     * {@link selectCollection()}.
     *
     * @see https://php.net/oop5.overloading#object.get
     * @see https://php.net/types.string#language.types.string.parsing.complex
     * @param string $collectionName Name of the collection to select
     */
    public function __get(string $collectionName): Collection
    {
        return $this->getCollection($collectionName);
    }

    /**
     * Return the database name.
     */
    public function __toString(): string
    {
        return $this->databaseName;
    }

    /**
     * Runs an aggregation framework pipeline on the database for pipeline
     * stages that do not require an underlying collection, such as $currentOp
     * and $listLocalSessions. Requires MongoDB >= 3.6
     *
     * @see Aggregate::__construct() for supported options
     * @param array|Pipeline $pipeline Aggregation pipeline
     * @param array          $options  Command options
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function aggregate(array|Pipeline $pipeline, array $options = []): CursorInterface
    {
        if (is_array($pipeline) && is_builder_pipeline($pipeline)) {
            $pipeline = new Pipeline(...$pipeline);
        }

        $pipeline = $this->builderEncoder->encodeIfSupported($pipeline);

        $hasWriteStage = is_last_pipeline_operator_write($pipeline);

        if (! isset($options['readPreference']) && ! is_in_transaction($options)) {
            $options['readPreference'] = $this->readPreference;
        }

        $server = $hasWriteStage
            ? select_server_for_aggregate_write_stage($this->manager, $options)
            : select_server($this->manager, $options);

        /* MongoDB 4.2 and later supports a read concern when an $out stage is
         * being used, but earlier versions do not.
         *
         * A read concern is also not compatible with transactions.
         */
        if (
            ! isset($options['readConcern']) &&
            ! is_in_transaction($options)
        ) {
            $options['readConcern'] = $this->readConcern;
        }

        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        if ($hasWriteStage && ! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new Aggregate($this->databaseName, null, $pipeline, $options);

        return $operation->execute($server);
    }

    /**
     * Execute a command on this database.
     *
     * @see DatabaseCommand::__construct() for supported options
     * @param array|object $command Command document
     * @param array        $options Options for command execution
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function command(array|object $command, array $options = []): CursorInterface
    {
        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $operation = new DatabaseCommand($this->databaseName, $command, $options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * Create a new collection explicitly.
     *
     * If the "encryptedFields" option is specified, this method additionally
     * creates related metadata collections and an index on the encrypted
     * collection.
     *
     * @see CreateCollection::__construct() for supported options
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/client-side-encryption.md#create-collection-helper
     * @see https://www.mongodb.com/docs/manual/core/queryable-encryption/fundamentals/manage-collections/
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function createCollection(string $collectionName, array $options = []): void
    {
        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        if (! isset($options['encryptedFields'])) {
            $options['encryptedFields'] = get_encrypted_fields_from_driver($this->databaseName, $collectionName, $this->manager);
        }

        $operation = isset($options['encryptedFields'])
            ? new CreateEncryptedCollection($this->databaseName, $collectionName, $options)
            : new CreateCollection($this->databaseName, $collectionName, $options);

        $server = select_server_for_write($this->manager, $options);

        $operation->execute($server);
    }

    /**
     * Create a new encrypted collection explicitly.
     *
     * The "encryptedFields" option is required.
     *
     * This method will automatically create data keys for any encrypted fields
     * where "keyId" is null. A copy of the modified "encryptedFields" option
     * will be returned in addition to the result from creating the collection.
     *
     * If any error is encountered creating data keys or the collection, a
     * CreateEncryptedCollectionException will be thrown. The original exception
     * and modified "encryptedFields" option can be accessed via the
     * getPrevious() and getEncryptedFields() methods, respectively.
     *
     * @see CreateCollection::__construct() for supported options
     * @return array The modified "encryptedFields" option
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws CreateEncryptedCollectionException for any errors creating data keys or creating the collection
     * @throws UnsupportedException if Queryable Encryption is not supported by the selected server
     */
    public function createEncryptedCollection(string $collectionName, ClientEncryption $clientEncryption, string $kmsProvider, ?array $masterKey, array $options): array
    {
        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new CreateEncryptedCollection($this->databaseName, $collectionName, $options);
        $server = select_server_for_write($this->manager, $options);

        try {
            $encryptedFields = $operation->createDataKeys($clientEncryption, $kmsProvider, $masterKey);
            $operation->execute($server);

            return $encryptedFields;
        } catch (Throwable $e) {
            throw new CreateEncryptedCollectionException($e, $encryptedFields ?? []);
        }
    }

    /**
     * Drop this database.
     *
     * @see DropDatabase::__construct() for supported options
     * @param array $options Additional options
     * @throws UnsupportedException if options are unsupported on the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function drop(array $options = []): void
    {
        $server = select_server_for_write($this->manager, $options);

        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new DropDatabase($this->databaseName, $options);

        $operation->execute($server);
    }

    /**
     * Drop a collection within this database.
     *
     * @see DropCollection::__construct() for supported options
     * @param string $collectionName Collection name
     * @param array  $options        Additional options
     * @throws UnsupportedException if options are unsupported on the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function dropCollection(string $collectionName, array $options = []): void
    {
        $server = select_server_for_write($this->manager, $options);

        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        if ($this->autoEncryptionEnabled && ! isset($options['encryptedFields'])) {
            $options['encryptedFields'] = get_encrypted_fields_from_driver($this->databaseName, $collectionName, $this->manager)
                ?? get_encrypted_fields_from_server($this->databaseName, $collectionName, $server);
        }

        $operation = isset($options['encryptedFields'])
            ? new DropEncryptedCollection($this->databaseName, $collectionName, $options)
            : new DropCollection($this->databaseName, $collectionName, $options);

        $operation->execute($server);
    }

    /**
     * Returns a collection instance.
     *
     * If the collection does not exist in the database, it is not created when
     * invoking this method.
     *
     * @see Collection::__construct() for supported options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function getCollection(string $collectionName, array $options = []): Collection
    {
        $options += [
            'builderEncoder' => $this->builderEncoder,
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
            'autoEncryptionEnabled' => $this->autoEncryptionEnabled,
        ];

        return new Collection($this->manager, $this->databaseName, $collectionName, $options);
    }

    /**
     * Returns the database name.
     */
    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    /**
     * Returns a GridFS bucket instance.
     *
     * @see Bucket::__construct() for supported options
     * @param array $options Bucket constructor options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function getGridFSBucket(array $options = []): Bucket
    {
        $options += [
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];

        return new Bucket($this->manager, $this->databaseName, $options);
    }

    /**
     * Return the Manager.
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }

    /**
     * Return the read concern for this database.
     *
     * @see https://php.net/manual/en/mongodb-driver-readconcern.isdefault.php
     */
    public function getReadConcern(): ReadConcern
    {
        return $this->readConcern;
    }

    /**
     * Return the read preference for this database.
     */
    public function getReadPreference(): ReadPreference
    {
        return $this->readPreference;
    }

    /**
     * Return the type map for this database.
     */
    public function getTypeMap(): array
    {
        return $this->typeMap;
    }

    /**
     * Return the write concern for this database.
     *
     * @see https://php.net/manual/en/mongodb-driver-writeconcern.isdefault.php
     */
    public function getWriteConcern(): WriteConcern
    {
        return $this->writeConcern;
    }

    /**
     * Returns the names of all collections in this database
     *
     * @see ListCollectionNames::__construct() for supported options
     * @return Iterator<int, string>
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function listCollectionNames(array $options = []): Iterator
    {
        $operation = new ListCollectionNames($this->databaseName, $options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * Returns information for all collections in this database.
     *
     * @see ListCollections::__construct() for supported options
     * @return Iterator<int, CollectionInfo>
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function listCollections(array $options = []): Iterator
    {
        $operation = new ListCollections($this->databaseName, $options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * Modifies a collection or view.
     *
     * @see ModifyCollection::__construct() for supported options
     * @param string $collectionName    Collection or view to modify
     * @param array  $collectionOptions Collection or view options to assign
     * @param array  $options           Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function modifyCollection(string $collectionName, array $collectionOptions, array $options = []): array|object
    {
        if (! isset($options['typeMap'])) {
            $options['typeMap'] = $this->typeMap;
        }

        $server = select_server_for_write($this->manager, $options);

        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new ModifyCollection($this->databaseName, $collectionName, $collectionOptions, $options);

        return $operation->execute($server);
    }

    /**
     * Rename a collection within this database.
     *
     * @see RenameCollection::__construct() for supported options
     * @param string      $fromCollectionName Collection name
     * @param string      $toCollectionName   New name of the collection
     * @param string|null $toDatabaseName     New database name of the collection. Defaults to the original database.
     * @param array       $options            Additional options
     * @throws UnsupportedException if options are unsupported on the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function renameCollection(string $fromCollectionName, string $toCollectionName, ?string $toDatabaseName = null, array $options = []): void
    {
        if (! isset($toDatabaseName)) {
            $toDatabaseName = $this->databaseName;
        }

        $server = select_server_for_write($this->manager, $options);

        if (! isset($options['writeConcern']) && ! is_in_transaction($options)) {
            $options['writeConcern'] = $this->writeConcern;
        }

        $operation = new RenameCollection($this->databaseName, $fromCollectionName, $toDatabaseName, $toCollectionName, $options);

        $operation->execute($server);
    }

    /**
     * Select a collection within this database.
     *
     * @see Collection::__construct() for supported options
     * @param string $collectionName Name of the collection to select
     * @param array  $options        Collection constructor options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function selectCollection(string $collectionName, array $options = []): Collection
    {
        return $this->getCollection($collectionName, $options);
    }

    /**
     * Select a GridFS bucket within this database.
     *
     * @see Bucket::__construct() for supported options
     * @param array $options Bucket constructor options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function selectGridFSBucket(array $options = []): Bucket
    {
        return $this->getGridFSBucket($options);
    }

    /**
     * Create a change stream for watching changes to the database.
     *
     * @see Watch::__construct() for supported options
     * @param array|Pipeline $pipeline Aggregation pipeline
     * @param array          $options  Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function watch(array|Pipeline $pipeline = [], array $options = []): ChangeStream
    {
        if (is_array($pipeline) && is_builder_pipeline($pipeline)) {
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

        $operation = new Watch($this->manager, $this->databaseName, null, $pipeline, $options);

        return $operation->execute($server);
    }

    /**
     * Get a clone of this database with different options.
     *
     * @see Database::__construct() for supported options
     * @param array $options Database constructor options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function withOptions(array $options = []): Database
    {
        $options += [
            'autoEncryptionEnabled' => $this->autoEncryptionEnabled,
            'builderEncoder' => $this->builderEncoder,
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];

        return new Database($this->manager, $this->databaseName, $options);
    }
}
