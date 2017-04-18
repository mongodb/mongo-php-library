<?php

namespace MongoDB;

use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\IndexInfoIterator;
use MongoDB\Operation\Aggregate;
use MongoDB\Operation\BulkWrite;
use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\Count;
use MongoDB\Operation\DeleteMany;
use MongoDB\Operation\DeleteOne;
use MongoDB\Operation\Distinct;
use MongoDB\Operation\DropCollection;
use MongoDB\Operation\DropIndexes;
use MongoDB\Operation\Find;
use MongoDB\Operation\FindOne;
use MongoDB\Operation\FindOneAndDelete;
use MongoDB\Operation\FindOneAndReplace;
use MongoDB\Operation\FindOneAndUpdate;
use MongoDB\Operation\InsertMany;
use MongoDB\Operation\InsertOne;
use MongoDB\Operation\ListIndexes;
use MongoDB\Operation\ReplaceOne;
use MongoDB\Operation\UpdateMany;
use MongoDB\Operation\UpdateOne;

interface CollectionInterface
{
    /**
     * Return internal properties for debugging purposes.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.debuginfo
     * @param array
     */
    public function __debugInfo();

    /**
     * Return the collection namespace (e.g. "db.collection").
     *
     * @see https://docs.mongodb.org/manual/faq/developers/#faq-dev-namespace
     * @param string
     */
    public function __toString();

    /**
     * Executes an aggregation framework pipeline on the collection.
     *
     * Note: this method's return value depends on the MongoDB server version
     * and the "useCursor" option. If "useCursor" is true, a Cursor will be
     * returned; otherwise, an ArrayIterator is returned, which wraps the
     * "result" array from the command response document.
     *
     * Note: BSON deserialization of inline aggregation results (i.e. not using
     * a command cursor) does not yet support a custom type map
     * (depends on: https://jira.mongodb.org/browse/PHPC-314).
     *
     * @see Aggregate::__construct() for supported options
     * @param array $pipeline List of pipeline operations
     * @param array $options  Command options
     * @return \Traversable
     */
    public function aggregate(array $pipeline, array $options = []);

    /**
     * Executes multiple write operations.
     *
     * @see BulkWrite::__construct() for supported options
     * @param array[] $operations List of write operations
     * @param array   $options    Command options
     * @return BulkWriteResult
     */
    public function bulkWrite(array $operations, array $options = []);

    /**
     * Gets the number of documents matching the filter.
     *
     * @see Count::__construct() for supported options
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Command options
     * @return integer
     */
    public function count($filter = [], array $options = []);

    /**
     * Create a single index for the collection.
     *
     * @see Collection::createIndexes()
     * @param array|object $key     Document containing fields mapped to values,
     *                              which denote order or an index type
     * @param array        $options Index options
     * @return string The name of the created index
     */
    public function createIndex($key, array $options = []);

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
     * @see http://docs.mongodb.org/manual/reference/command/createIndexes/
     * @see http://docs.mongodb.org/manual/reference/method/db.collection.createIndex/
     * @param array[] $indexes List of index specifications
     * @return string[] The names of the created indexes
     * @throws InvalidArgumentException if an index specification is invalid
     */
    public function createIndexes(array $indexes);

    /**
     * Deletes all documents matching the filter.
     *
     * @see DeleteMany::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/delete/
     * @param array|object $filter  Query by which to delete documents
     * @param array        $options Command options
     * @return DeleteResult
     */
    public function deleteMany($filter, array $options = []);

    /**
     * Deletes at most one document matching the filter.
     *
     * @see DeleteOne::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/delete/
     * @param array|object $filter  Query by which to delete documents
     * @param array        $options Command options
     * @return DeleteResult
     */
    public function deleteOne($filter, array $options = []);

    /**
     * Finds the distinct values for a specified field across the collection.
     *
     * @see Distinct::__construct() for supported options
     * @param string $fieldName Field for which to return distinct values
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Command options
     * @return mixed[]
     */
    public function distinct($fieldName, $filter = [], array $options = []);

    /**
     * Drop this collection.
     *
     * @see DropCollection::__construct() for supported options
     * @param array $options Additional options
     * @return array|object Command result document
     */
    public function drop(array $options = []);

    /**
     * Drop a single index in the collection.
     *
     * @see DropIndexes::__construct() for supported options
     * @param string $indexName Index name
     * @param array  $options   Additional options
     * @return array|object Command result document
     * @throws InvalidArgumentException if $indexName is an empty string or "*"
     */
    public function dropIndex($indexName, array $options = []);

    /**
     * Drop all indexes in the collection.
     *
     * @see DropIndexes::__construct() for supported options
     * @param array $options Additional options
     * @return array|object Command result document
     */
    public function dropIndexes(array $options = []);

    /**
     * Finds documents matching the query.
     *
     * @see Find::__construct() for supported options
     * @see http://docs.mongodb.org/manual/core/read-operations-introduction/
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return Cursor
     */
    public function find($filter = [], array $options = []);

    /**
     * Finds a single document matching the query.
     *
     * @see FindOne::__construct() for supported options
     * @see http://docs.mongodb.org/manual/core/read-operations-introduction/
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return array|object|null
     */
    public function findOne($filter = [], array $options = []);

    /**
     * Finds a single document and deletes it, returning the original.
     *
     * The document to return may be null if no document matched the filter.
     *
     * Note: BSON deserialization of the returned document does not yet support
     * a custom type map (depends on: https://jira.mongodb.org/browse/PHPC-314).
     *
     * @see FindOneAndDelete::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Command options
     * @return object|null
     */
    public function findOneAndDelete($filter, array $options = []);

    /**
     * Finds a single document and replaces it, returning either the original or
     * the replaced document.
     *
     * The document to return may be null if no document matched the filter. By
     * default, the original document is returned. Specify
     * FindOneAndReplace::RETURN_DOCUMENT_AFTER for the "returnDocument" option
     * to return the updated document.
     *
     * Note: BSON deserialization of the returned document does not yet support
     * a custom type map (depends on: https://jira.mongodb.org/browse/PHPC-314).
     *
     * @see FindOneAndReplace::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
     * @param array|object $filter      Query by which to filter documents
     * @param array|object $replacement Replacement document
     * @param array        $options     Command options
     * @return object|null
     */
    public function findOneAndReplace($filter, $replacement, array $options = []);

    /**
     * Finds a single document and updates it, returning either the original or
     * the updated document.
     *
     * The document to return may be null if no document matched the filter. By
     * default, the original document is returned. Specify
     * FindOneAndUpdate::RETURN_DOCUMENT_AFTER for the "returnDocument" option
     * to return the updated document.
     *
     * Note: BSON deserialization of the returned document does not yet support
     * a custom type map (depends on: https://jira.mongodb.org/browse/PHPC-314).
     *
     * @see FindOneAndReplace::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
     * @param array|object $filter  Query by which to filter documents
     * @param array|object $update  Update to apply to the matched document
     * @param array        $options Command options
     * @return object|null
     */
    public function findOneAndUpdate($filter, $update, array $options = []);

    /**
     * Return the collection name.
     *
     * @return string
     */
    public function getCollectionName();

    /**
     * Return the database name.
     *
     * @return string
     */
    public function getDatabaseName();

    /**
     * Return the collection namespace.
     *
     * @see https://docs.mongodb.org/manual/reference/glossary/#term-namespace
     * @return string
     */
    public function getNamespace();

    /**
     * Inserts multiple documents.
     *
     * @see InsertMany::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/insert/
     * @param array[]|object[] $documents The documents to insert
     * @param array            $options   Command options
     * @return InsertManyResult
     */
    public function insertMany(array $documents, array $options = []);

    /**
     * Inserts one document.
     *
     * @see InsertOne::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/insert/
     * @param array|object $document The document to insert
     * @param array        $options  Command options
     * @return InsertOneResult
     */
    public function insertOne($document, array $options = []);

    /**
     * Returns information for all indexes for the collection.
     *
     * @see ListIndexes::__construct() for supported options
     * @return IndexInfoIterator
     */
    public function listIndexes(array $options = []);

    /**
     * Replaces at most one document matching the filter.
     *
     * @see ReplaceOne::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/update/
     * @param array|object $filter      Query by which to filter documents
     * @param array|object $replacement Replacement document
     * @param array        $options     Command options
     * @return UpdateResult
     */
    public function replaceOne($filter, $replacement, array $options = []);

    /**
     * Updates all documents matching the filter.
     *
     * @see UpdateMany::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/update/
     * @param array|object $filter  Query by which to filter documents
     * @param array|object $update  Update to apply to the matched documents
     * @param array        $options Command options
     * @return UpdateResult
     */
    public function updateMany($filter, $update, array $options = []);

    /**
     * Updates at most one document matching the filter.
     *
     * @see UpdateOne::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/update/
     * @param array|object $filter  Query by which to filter documents
     * @param array|object $update  Update to apply to the matched document
     * @param array        $options Command options
     * @return UpdateResult
     */
    public function updateOne($filter, $update, array $options = []);
}
