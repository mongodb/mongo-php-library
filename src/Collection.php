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

use Countable;
use Iterator;
use MongoDB\BSON\JavascriptInterface;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Driver\CursorInterface;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Exception\UnsupportedException;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\IndexInfo;
use MongoDB\Operation\Aggregate;
use MongoDB\Operation\BulkWrite;
use MongoDB\Operation\Count;
use MongoDB\Operation\CountDocuments;
use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\CreateSearchIndexes;
use MongoDB\Operation\DeleteMany;
use MongoDB\Operation\DeleteOne;
use MongoDB\Operation\Distinct;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\DropEncryptedCollection;
use MongoDB\Operation\DropIndexes;
use MongoDB\Operation\DropSearchIndex;
use MongoDB\Operation\EstimatedDocumentCount;
use MongoDB\Operation\Explain;
use MongoDB\Operation\Explainable;
use MongoDB\Operation\Find;
use MongoDB\Operation\FindOne;
use MongoDB\Operation\FindOneAndDelete;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Operation\FindOneAndUpdate;
use MongoDB\Operation\InsertMany;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListIndexes;
use MongoDB\Operation\ListSearchIndexes;
use MongoDB\Operation\MapReduce;
use MongoDB\Operation\RenameCollection;
use MongoDB\Operation\ReplaceOne;
use MongoDB\Operation\UpdateMany;
use MongoDB\Operation\UpdateOne;
use MongoDB\Operation\UpdateSearchIndex;
use MongoDB\Operation\Watch;

use function array_diff_key;
use function array_intersect_key;
use function array_key_exists;
use function current;
use function is_array;
use function strlen;

class Collection
{
    private const DEFAULT_TYPE_MAP = [
        'array' => BSONArray::class,
        'document' => BSONDocument::class,
        'root' => BSONDocument::class,
    ];

    private const WIRE_VERSION_FOR_READ_CONCERN_WITH_WRITE_STAGE = 8;

    private ?DocumentCodec $codec = null;

    private string $collectionName;

    private string $databaseName;

    private Manager $manager;

    private ReadConcern $readConcern;

    private ReadPreference $readPreference;

    private array $typeMap;

    private WriteConcern $writeConcern;

    /**
     * Constructs new Collection instance.
     *
     * This class provides methods for collection-specific operations, such as
     * CRUD (i.e. create, read, update, and delete) and index management.
     *
     * Supported options:
     *
     *  * codec (MongoDB\Codec\DocumentCodec): Codec used to decode documents
     *    from BSON to PHP objects.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): The default read concern to
     *    use for collection operations. Defaults to the Manager's read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The default read
     *    preference to use for collection operations. Defaults to the Manager's
     *    read preference.
     *
     *  * typeMap (array): Default type map for cursors and BSON documents.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): The default write concern
     *    to use for collection operations. Defaults to the Manager's write
     *    concern.
     *
     * @param Manager $manager        Manager instance from the driver
     * @param string  $databaseName   Database name
     * @param string  $collectionName Collection name
     * @param array   $options        Collection options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(Manager $manager, string $databaseName, string $collectionName, array $options = [])
    {
        if (strlen($databaseName) < 1) {
            throw new InvalidArgumentException('$databaseName is invalid: ' . $databaseName);
        }

        if (strlen($collectionName) < 1) {
            throw new InvalidArgumentException('$collectionName is invalid: ' . $collectionName);
        }

        if (isset($options['codec']) && ! $options['codec'] instanceof DocumentCodec) {
            throw InvalidArgumentException::invalidType('"codec" option', $options['codec'], DocumentCodec::class);
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

        $this->manager = $manager;
        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;

        $this->codec = $options['codec'] ?? null;
        $this->readConcern = $options['readConcern'] ?? $this->manager->getReadConcern();
        $this->readPreference = $options['readPreference'] ?? $this->manager->getReadPreference();
        $this->typeMap = $options['typeMap'] ?? self::DEFAULT_TYPE_MAP;
        $this->writeConcern = $options['writeConcern'] ?? $this->manager->getWriteConcern();
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
            'codec' => $this->codec,
            'collectionName' => $this->collectionName,
            'databaseName' => $this->databaseName,
            'manager' => $this->manager,
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];
    }

    /**
     * Return the collection namespace (e.g. "db.collection").
     *
     * @see https://mongodb.com/docs/manual/core/databases-and-collections/
     * @return string
     */
    public function __toString()
    {
        return $this->databaseName . '.' . $this->collectionName;
    }

    /**
     * Executes an aggregation framework pipeline on the collection.
     *
     * @see Aggregate::__construct() for supported options
     * @param array $pipeline Aggregation pipeline
     * @param array $options  Command options
     * @return CursorInterface&Iterator
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function aggregate(array $pipeline, array $options = [])
    {
        $hasWriteStage = is_last_pipeline_operator_write($pipeline);

        $options = $this->inheritReadPreference($options);

        $server = $hasWriteStage
            ? select_server_for_aggregate_write_stage($this->manager, $options)
            : select_server($this->manager, $options);

        /* MongoDB 4.2 and later supports a read concern when an $out stage is
         * being used, but earlier versions do not.
         */
        if (! $hasWriteStage || server_supports_feature($server, self::WIRE_VERSION_FOR_READ_CONCERN_WITH_WRITE_STAGE)) {
            $options = $this->inheritReadConcern($options);
        }

        $options = $this->inheritCodecOrTypeMap($options);

        if ($hasWriteStage) {
            $options = $this->inheritWriteOptions($options);
        }

        $operation = new Aggregate($this->databaseName, $this->collectionName, $pipeline, $options);

        return $operation->execute($server);
    }

    /**
     * Executes multiple write operations.
     *
     * @see BulkWrite::__construct() for supported options
     * @param array[] $operations List of write operations
     * @param array   $options    Command options
     * @return BulkWriteResult
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function bulkWrite(array $operations, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritCodec($options);

        $operation = new BulkWrite($this->databaseName, $this->collectionName, $operations, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Gets the number of documents matching the filter.
     *
     * @see Count::__construct() for supported options
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Command options
     * @return integer
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     *
     * @deprecated 1.4
     */
    public function count($filter = [], array $options = [])
    {
        $options = $this->inheritReadOptions($options);

        $operation = new Count($this->databaseName, $this->collectionName, $filter, $options);

        return $operation->execute(select_server($this->manager, $options));
    }

    /**
     * Gets the number of documents matching the filter.
     *
     * @see CountDocuments::__construct() for supported options
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Command options
     * @return integer
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function countDocuments($filter = [], array $options = [])
    {
        $options = $this->inheritReadOptions($options);

        $operation = new CountDocuments($this->databaseName, $this->collectionName, $filter, $options);

        return $operation->execute(select_server($this->manager, $options));
    }

    /**
     * Create a single index for the collection.
     *
     * @see Collection::createIndexes()
     * @see CreateIndexes::__construct() for supported command options
     * @param array|object $key     Document containing fields mapped to values,
     *                              which denote order or an index type
     * @param array        $options Index and command options
     * @return string The name of the created index
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function createIndex($key, array $options = [])
    {
        $operationOptionKeys = ['comment' => 1, 'commitQuorum' => 1, 'maxTimeMS' => 1, 'session' => 1, 'writeConcern' => 1];
        $indexOptions = array_diff_key($options, $operationOptionKeys);
        $operationOptions = array_intersect_key($options, $operationOptionKeys);

        return current($this->createIndexes([['key' => $key] + $indexOptions], $operationOptions));
    }

    /**
     * Create one or more indexes for the collection.
     *
     * Each element in the $indexes array must have a "key" document, which
     * contains fields mapped to an order or type. Other options may follow.
     * For example:
     *
     *     $indexes = [
     *         // Create a unique index on the "username" field
     *         [ 'key' => [ 'username' => 1 ], 'unique' => true ],
     *         // Create a 2dsphere index on the "loc" field with a custom name
     *         [ 'key' => [ 'loc' => '2dsphere' ], 'name' => 'geo' ],
     *     ];
     *
     * If the "name" option is unspecified, a name will be generated from the
     * "key" document.
     *
     * @see https://mongodb.com/docs/manual/reference/command/createIndexes/
     * @see https://mongodb.com/docs/manual/reference/method/db.collection.createIndex/
     * @see CreateIndexes::__construct() for supported command options
     * @param array[] $indexes List of index specifications
     * @param array   $options Command options
     * @return string[] The names of the created indexes
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function createIndexes(array $indexes, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);

        $operation = new CreateIndexes($this->databaseName, $this->collectionName, $indexes, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Create an Atlas Search index for the collection.
     * Only available when used against a 7.0+ Atlas cluster.
     *
     * @see https://www.mongodb.com/docs/manual/reference/command/createSearchIndexes/
     * @see https://mongodb.com/docs/manual/reference/method/db.collection.createSearchIndex/
     * @param array|object                                         $definition Atlas Search index mapping definition
     * @param array{comment?: mixed, name?: string, type?: string} $options    Index and command options
     * @return string The name of the created search index
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function createSearchIndex($definition, array $options = []): string
    {
        $indexOptionKeys = ['name' => 1, 'type' => 1];
        /** @psalm-var array{name?: string, type?: string} */
        $indexOptions = array_intersect_key($options, $indexOptionKeys);
        /** @psalm-var array{comment?: mixed} */
        $operationOptions = array_diff_key($options, $indexOptionKeys);

        $names = $this->createSearchIndexes([['definition' => $definition] + $indexOptions], $operationOptions);

        return current($names);
    }

    /**
     * Create one or more Atlas Search indexes for the collection.
     * Only available when used against a 7.0+ Atlas cluster.
     *
     * Each element in the $indexes array must have "definition" document and they may have a "name" string.
     * The name can be omitted for a single index, in which case a name will be the default.
     * For example:
     *
     *     $indexes = [
     *         // Create a search index with the default name on a single field
     *         ['definition' => ['mappings' => ['dynamic' => false, 'fields' => ['title' => ['type' => 'string']]]]],
     *         // Create a named search index on all fields
     *         ['name' => 'search_all', 'definition' => ['mappings' => ['dynamic' => true]]],
     *     ];
     *
     * @see https://www.mongodb.com/docs/manual/reference/command/createSearchIndexes/
     * @see https://mongodb.com/docs/manual/reference/method/db.collection.createSearchIndex/
     * @param list<array{definition: array|object, name?: string, type?: string}> $indexes List of search index specifications
     * @param array{comment?: mixed}                                              $options Command options
     * @return string[] The names of the created search indexes
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function createSearchIndexes(array $indexes, array $options = []): array
    {
        $operation = new CreateSearchIndexes($this->databaseName, $this->collectionName, $indexes, $options);
        $server = select_server_for_write($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * Deletes all documents matching the filter.
     *
     * @see DeleteMany::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/delete/
     * @param array|object $filter  Query by which to delete documents
     * @param array        $options Command options
     * @return DeleteResult
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function deleteMany($filter, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);

        $operation = new DeleteMany($this->databaseName, $this->collectionName, $filter, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Deletes at most one document matching the filter.
     *
     * @see DeleteOne::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/delete/
     * @param array|object $filter  Query by which to delete documents
     * @param array        $options Command options
     * @return DeleteResult
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function deleteOne($filter, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);

        $operation = new DeleteOne($this->databaseName, $this->collectionName, $filter, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Finds the distinct values for a specified field across the collection.
     *
     * @see Distinct::__construct() for supported options
     * @param string       $fieldName Field for which to return distinct values
     * @param array|object $filter    Query by which to filter documents
     * @param array        $options   Command options
     * @return array
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function distinct(string $fieldName, $filter = [], array $options = [])
    {
        $options = $this->inheritReadOptions($options);
        $options = $this->inheritTypeMap($options);

        $operation = new Distinct($this->databaseName, $this->collectionName, $fieldName, $filter, $options);

        return $operation->execute(select_server($this->manager, $options));
    }

    /**
     * Drop this collection.
     *
     * @see DropCollection::__construct() for supported options
     * @param array $options Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function drop(array $options = [])
    {
        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritTypeMap($options);

        $server = select_server_for_write($this->manager, $options);

        if (! isset($options['encryptedFields'])) {
            $options['encryptedFields'] = get_encrypted_fields_from_driver($this->databaseName, $this->collectionName, $this->manager)
                ?? get_encrypted_fields_from_server($this->databaseName, $this->collectionName, $this->manager, $server);
        }

        $operation = isset($options['encryptedFields'])
            ? new DropEncryptedCollection($this->databaseName, $this->collectionName, $options)
            : new DropCollection($this->databaseName, $this->collectionName, $options);

        return $operation->execute($server);
    }

    /**
     * Drop a single index in the collection.
     *
     * @see DropIndexes::__construct() for supported options
     * @param string|IndexInfo $indexName Index name or model object
     * @param array            $options   Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function dropIndex($indexName, array $options = [])
    {
        $indexName = (string) $indexName;

        if ($indexName === '*') {
            throw new InvalidArgumentException('dropIndexes() must be used to drop multiple indexes');
        }

        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritTypeMap($options);

        $operation = new DropIndexes($this->databaseName, $this->collectionName, $indexName, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Drop all indexes in the collection.
     *
     * @see DropIndexes::__construct() for supported options
     * @param array $options Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function dropIndexes(array $options = [])
    {
        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritTypeMap($options);

        $operation = new DropIndexes($this->databaseName, $this->collectionName, '*', $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Drop a single Atlas Search index in the collection.
     * Only available when used against a 7.0+ Atlas cluster.
     *
     * @param string                 $name    Search index name
     * @param array{comment?: mixed} $options Additional options
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function dropSearchIndex(string $name, array $options = []): void
    {
        $operation = new DropSearchIndex($this->databaseName, $this->collectionName, $name);
        $server = select_server_for_write($this->manager, $options);

        $operation->execute($server);
    }

    /**
     * Gets an estimated number of documents in the collection using the collection metadata.
     *
     * @see EstimatedDocumentCount::__construct() for supported options
     * @param array $options Command options
     * @return integer
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function estimatedDocumentCount(array $options = [])
    {
        $options = $this->inheritReadOptions($options);

        $operation = new EstimatedDocumentCount($this->databaseName, $this->collectionName, $options);

        return $operation->execute(select_server($this->manager, $options));
    }

    /**
     * Explains explainable commands.
     *
     * @see Explain::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/explain/
     * @param Explainable $explainable Command on which to run explain
     * @param array       $options     Additional options
     * @return array|object
     * @throws UnsupportedException if explainable or options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function explain(Explainable $explainable, array $options = [])
    {
        $options = $this->inheritReadPreference($options);
        $options = $this->inheritTypeMap($options);

        $operation = new Explain($this->databaseName, $explainable, $options);

        return $operation->execute(select_server($this->manager, $options));
    }

    /**
     * Finds documents matching the query.
     *
     * @see Find::__construct() for supported options
     * @see https://mongodb.com/docs/manual/crud/#read-operations
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return CursorInterface&Iterator
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function find($filter = [], array $options = [])
    {
        $options = $this->inheritReadOptions($options);
        $options = $this->inheritCodecOrTypeMap($options);

        $operation = new Find($this->databaseName, $this->collectionName, $filter, $options);

        return $operation->execute(select_server($this->manager, $options));
    }

    /**
     * Finds a single document matching the query.
     *
     * @see FindOne::__construct() for supported options
     * @see https://mongodb.com/docs/manual/crud/#read-operations
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return array|object|null
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function findOne($filter = [], array $options = [])
    {
        $options = $this->inheritReadOptions($options);
        $options = $this->inheritCodecOrTypeMap($options);

        $operation = new FindOne($this->databaseName, $this->collectionName, $filter, $options);

        return $operation->execute(select_server($this->manager, $options));
    }

    /**
     * Finds a single document and deletes it, returning the original.
     *
     * The document to return may be null if no document matched the filter.
     *
     * @see FindOneAndDelete::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/findAndModify/
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Command options
     * @return array|object|null
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function findOneAndDelete($filter, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritCodecOrTypeMap($options);

        $operation = new FindOneAndDelete($this->databaseName, $this->collectionName, $filter, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Finds a single document and replaces it, returning either the original or
     * the replaced document.
     *
     * The document to return may be null if no document matched the filter. By
     * default, the original document is returned. Specify
     * FindOneAndReplace::RETURN_DOCUMENT_AFTER for the "returnDocument" option
     * to return the updated document.
     *
     * @see FindOneAndReplace::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/findAndModify/
     * @param array|object $filter      Query by which to filter documents
     * @param array|object $replacement Replacement document
     * @param array        $options     Command options
     * @return array|object|null
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function findOneAndReplace($filter, $replacement, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritCodecOrTypeMap($options);

        $operation = new FindOneAndReplace($this->databaseName, $this->collectionName, $filter, $replacement, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Finds a single document and updates it, returning either the original or
     * the updated document.
     *
     * The document to return may be null if no document matched the filter. By
     * default, the original document is returned. Specify
     * FindOneAndUpdate::RETURN_DOCUMENT_AFTER for the "returnDocument" option
     * to return the updated document.
     *
     * @see FindOneAndReplace::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/findAndModify/
     * @param array|object $filter  Query by which to filter documents
     * @param array|object $update  Update to apply to the matched document
     * @param array        $options Command options
     * @return array|object|null
     * @throws UnexpectedValueException if the command response was malformed
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function findOneAndUpdate($filter, $update, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritCodecOrTypeMap($options);

        $operation = new FindOneAndUpdate($this->databaseName, $this->collectionName, $filter, $update, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Return the collection name.
     *
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collectionName;
    }

    /**
     * Return the database name.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
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
     * Return the collection namespace.
     *
     * @see https://mongodb.com/docs/manual/reference/glossary/#term-namespace
     * @return string
     */
    public function getNamespace()
    {
        return $this->databaseName . '.' . $this->collectionName;
    }

    /**
     * Return the read concern for this collection.
     *
     * @see https://php.net/manual/en/mongodb-driver-readconcern.isdefault.php
     * @return ReadConcern
     */
    public function getReadConcern()
    {
        return $this->readConcern;
    }

    /**
     * Return the read preference for this collection.
     *
     * @return ReadPreference
     */
    public function getReadPreference()
    {
        return $this->readPreference;
    }

    /**
     * Return the type map for this collection.
     *
     * @return array
     */
    public function getTypeMap()
    {
        return $this->typeMap;
    }

    /**
     * Return the write concern for this collection.
     *
     * @see https://php.net/manual/en/mongodb-driver-writeconcern.isdefault.php
     * @return WriteConcern
     */
    public function getWriteConcern()
    {
        return $this->writeConcern;
    }

    /**
     * Inserts multiple documents.
     *
     * @see InsertMany::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/insert/
     * @param list<object|array> $documents The documents to insert
     * @param array              $options   Command options
     * @return InsertManyResult
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function insertMany(array $documents, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritCodec($options);

        $operation = new InsertMany($this->databaseName, $this->collectionName, $documents, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Inserts one document.
     *
     * @see InsertOne::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/insert/
     * @param array|object $document The document to insert
     * @param array        $options  Command options
     * @return InsertOneResult
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function insertOne($document, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritCodec($options);

        $operation = new InsertOne($this->databaseName, $this->collectionName, $document, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Returns information for all indexes for the collection.
     *
     * @see ListIndexes::__construct() for supported options
     * @return Iterator<int, IndexInfo>
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function listIndexes(array $options = [])
    {
        $operation = new ListIndexes($this->databaseName, $this->collectionName, $options);

        return $operation->execute(select_server($this->manager, $options));
    }

    /**
     * Returns information for all Atlas Search indexes for the collection.
     * Only available when used against a 7.0+ Atlas cluster.
     *
     * @param array{name?: string} $options Command options
     * @return Countable&Iterator
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     * @see ListSearchIndexes::__construct() for supported options
     */
    public function listSearchIndexes(array $options = []): Iterator
    {
        $operation = new ListSearchIndexes($this->databaseName, $this->collectionName, $options);
        $server = select_server($this->manager, $options);

        return $operation->execute($server);
    }

    /**
     * Executes a map-reduce aggregation on the collection.
     *
     * @see MapReduce::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/mapReduce/
     * @param JavascriptInterface $map     Map function
     * @param JavascriptInterface $reduce  Reduce function
     * @param string|array|object $out     Output specification
     * @param array               $options Command options
     * @return MapReduceResult
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     * @throws UnexpectedValueException if the command response was malformed
     */
    public function mapReduce(JavascriptInterface $map, JavascriptInterface $reduce, $out, array $options = [])
    {
        $hasOutputCollection = ! is_mapreduce_output_inline($out);

        // Check if the out option is inline because we will want to coerce a primary read preference if not
        if ($hasOutputCollection) {
            $options['readPreference'] = new ReadPreference(ReadPreference::PRIMARY);
        } else {
            $options = $this->inheritReadPreference($options);
        }

        /* A "majority" read concern is not compatible with inline output, so
         * avoid providing the Collection's read concern if it would conflict.
         */
        if (! $hasOutputCollection || $this->readConcern->getLevel() !== ReadConcern::MAJORITY) {
            $options = $this->inheritReadConcern($options);
        }

        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritTypeMap($options);

        $operation = new MapReduce($this->databaseName, $this->collectionName, $map, $reduce, $out, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Renames the collection.
     *
     * @see RenameCollection::__construct() for supported options
     * @param string      $toCollectionName New name of the collection
     * @param string|null $toDatabaseName   New database name of the collection. Defaults to the original database.
     * @param array       $options          Additional options
     * @return array|object Command result document
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function rename(string $toCollectionName, ?string $toDatabaseName = null, array $options = [])
    {
        if (! isset($toDatabaseName)) {
            $toDatabaseName = $this->databaseName;
        }

        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritTypeMap($options);

        $operation = new RenameCollection($this->databaseName, $this->collectionName, $toDatabaseName, $toCollectionName, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Replaces at most one document matching the filter.
     *
     * @see ReplaceOne::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/update/
     * @param array|object $filter      Query by which to filter documents
     * @param array|object $replacement Replacement document
     * @param array        $options     Command options
     * @return UpdateResult
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function replaceOne($filter, $replacement, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);
        $options = $this->inheritCodec($options);

        $operation = new ReplaceOne($this->databaseName, $this->collectionName, $filter, $replacement, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Updates all documents matching the filter.
     *
     * @see UpdateMany::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/update/
     * @param array|object $filter  Query by which to filter documents
     * @param array|object $update  Update to apply to the matched documents
     * @param array        $options Command options
     * @return UpdateResult
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function updateMany($filter, $update, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);

        $operation = new UpdateMany($this->databaseName, $this->collectionName, $filter, $update, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Updates at most one document matching the filter.
     *
     * @see UpdateOne::__construct() for supported options
     * @see https://mongodb.com/docs/manual/reference/command/update/
     * @param array|object $filter  Query by which to filter documents
     * @param array|object $update  Update to apply to the matched document
     * @param array        $options Command options
     * @return UpdateResult
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter/option parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function updateOne($filter, $update, array $options = [])
    {
        $options = $this->inheritWriteOptions($options);

        $operation = new UpdateOne($this->databaseName, $this->collectionName, $filter, $update, $options);

        return $operation->execute(select_server_for_write($this->manager, $options));
    }

    /**
     * Update a single Atlas Search index in the collection.
     * Only available when used against a 7.0+ Atlas cluster.
     *
     * @param string                 $name       Search index name
     * @param array|object           $definition Atlas Search index definition
     * @param array{comment?: mixed} $options    Command options
     * @throws UnsupportedException if options are not supported by the selected server
     * @throws InvalidArgumentException for parameter parsing errors
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function updateSearchIndex(string $name, $definition, array $options = []): void
    {
        $operation = new UpdateSearchIndex($this->databaseName, $this->collectionName, $name, $definition, $options);
        $server = select_server_for_write($this->manager, $options);

        $operation->execute($server);
    }

    /**
     * Create a change stream for watching changes to the collection.
     *
     * @see Watch::__construct() for supported options
     * @param array $pipeline Aggregation pipeline
     * @param array $options  Command options
     * @return ChangeStream
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function watch(array $pipeline = [], array $options = [])
    {
        $options = $this->inheritReadOptions($options);
        $options = $this->inheritCodecOrTypeMap($options);

        $operation = new Watch($this->manager, $this->databaseName, $this->collectionName, $pipeline, $options);

        return $operation->execute(select_server($this->manager, $options));
    }

    /**
     * Get a clone of this collection with different options.
     *
     * @see Collection::__construct() for supported options
     * @param array $options Collection constructor options
     * @return Collection
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function withOptions(array $options = [])
    {
        $options += [
            'codec' => $this->codec,
            'readConcern' => $this->readConcern,
            'readPreference' => $this->readPreference,
            'typeMap' => $this->typeMap,
            'writeConcern' => $this->writeConcern,
        ];

        return new Collection($this->manager, $this->databaseName, $this->collectionName, $options);
    }

    private function inheritCodec(array $options): array
    {
        // If the options contain a type map, don't inherit anything
        if (isset($options['typeMap'])) {
            return $options;
        }

        if (! array_key_exists('codec', $options)) {
            $options['codec'] = $this->codec;
        }

        return $options;
    }

    private function inheritCodecOrTypeMap(array $options): array
    {
        // If the options contain a type map, don't inherit anything
        if (isset($options['typeMap'])) {
            return $options;
        }

        // If this collection does not use a codec, or if a codec was explicitly
        // defined in the options, only inherit the type map (if possible)
        if (! $this->codec || array_key_exists('codec', $options)) {
            return $this->inheritTypeMap($options);
        }

        // At this point, we know that we use a codec and the options array did
        // not explicitly contain a codec, so we can inherit ours
        $options['codec'] = $this->codec;

        return $options;
    }

    private function inheritReadConcern(array $options): array
    {
        // ReadConcern and ReadPreference may not change within a transaction
        if (! isset($options['readConcern']) && ! is_in_transaction($options)) {
            $options['readConcern'] = $this->readConcern;
        }

        return $options;
    }

    private function inheritReadOptions(array $options): array
    {
        $options = $this->inheritReadConcern($options);

        return $this->inheritReadPreference($options);
    }

    private function inheritReadPreference(array $options): array
    {
        // ReadConcern and ReadPreference may not change within a transaction
        if (! isset($options['readPreference']) && ! is_in_transaction($options)) {
            $options['readPreference'] = $this->readPreference;
        }

        return $options;
    }

    private function inheritTypeMap(array $options): array
    {
        // Only inherit the type map if no codec is used
        if (! isset($options['typeMap']) && ! isset($options['codec'])) {
            $options['typeMap'] = $this->typeMap;
        }

        return $options;
    }

    private function inheritWriteOptions(array $options): array
    {
        // WriteConcern may not change within a transaction
        if (! is_in_transaction($options)) {
            if (! isset($options['writeConcern'])) {
                $options['writeConcern'] = $this->writeConcern;
            }
        }

        return $options;
    }
}
