<?php

namespace MongoDB;

use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnexpectedTypeException;
use MongoDB\Model\IndexInfoIterator;
use MongoDB\Model\IndexInput;
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
use Traversable;

class Collection
{
    /* {{{ consts & vars */
    protected $manager;
    protected $ns;
    protected $wc;
    protected $rp;

    protected $dbname;
    protected $collname;
    /* }}} */


    /**
     * Constructs new Collection instance.
     *
     * This class provides methods for collection-specific operations, such as
     * CRUD (i.e. create, read, update, and delete) and index management.
     *
     * @param Manager        $manager        Manager instance from the driver
     * @param string         $namespace      Collection namespace (e.g. "db.collection")
     * @param WriteConcern   $writeConcern   Default write concern to apply
     * @param ReadPreference $readPreference Default read preference to apply
     */
    public function __construct(Manager $manager, $namespace, WriteConcern $writeConcern = null, ReadPreference $readPreference = null)
    {
        $this->manager = $manager;
        $this->ns = (string) $namespace;
        $this->wc = $writeConcern;
        $this->rp = $readPreference;

        list($this->dbname, $this->collname) = explode(".", $namespace, 2);
    }

    /**
     * Return the collection namespace.
     *
     * @param string
     */
    public function __toString()
    {
        return $this->ns;
    }

    /**
     * Executes an aggregation framework pipeline on the collection.
     *
     * Note: this method's return value depends on the MongoDB server version
     * and the "useCursor" option. If "useCursor" is true, a Cursor will be
     * returned; otherwise, an ArrayIterator is returned, which wraps the
     * "result" array from the command response document.
     *
     * @see Aggregate::__construct() for supported options
     * @param array $pipeline List of pipeline operations
     * @param array $options  Command options
     * @return Traversable
     */
    public function aggregate(array $pipeline, array $options = array())
    {
        $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
        $server = $this->manager->selectServer($readPreference);
        $operation = new Aggregate($this->dbname, $this->collname, $pipeline, $options);

        return $operation->execute($server);
    }

    /**
     * Executes multiple write operations.
     *
     * @see BulkWrite::__construct() for supported options
     * @param array[] $operations List of write operations
     * @param array   $options    Command options
     * @return BulkWriteResult
     */
    public function bulkWrite(array $operations, array $options = array())
    {
        if ( ! isset($options['writeConcern']) && isset($this->wc)) {
            $options['writeConcern'] = $this->wc;
        }

        $operation = new BulkWrite($this->dbname, $this->collname, $operations, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Gets the number of documents matching the filter.
     *
     * @see Count::__construct() for supported options
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Command options
     * @return integer
     */
    public function count($filter = array(), array $options = array())
    {
        $operation = new Count($this->dbname, $this->collname, $filter, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Create a single index for the collection.
     *
     * @see Collection::createIndexes()
     * @param array|object $key     Document containing fields mapped to values,
     *                              which denote order or an index type
     * @param array        $options Index options
     * @return string The name of the created index
     */
    public function createIndex($key, array $options = array())
    {
        return current($this->createIndexes(array(array('key' => $key) + $options)));
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
     * @see http://docs.mongodb.org/manual/reference/command/createIndexes/
     * @see http://docs.mongodb.org/manual/reference/method/db.collection.createIndex/
     * @param array[] $indexes List of index specifications
     * @return string[] The names of the created indexes
     * @throws InvalidArgumentException if an index specification is invalid
     */
    public function createIndexes(array $indexes)
    {
        $operation = new CreateIndexes($this->dbname, $this->collname, $indexes);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Deletes all documents matching the filter.
     *
     * @see DeleteMany::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/delete/
     * @param array|object $filter  Query by which to delete documents
     * @param array        $options Command options
     * @return DeleteResult
     */
    public function deleteMany($filter, array $options = array())
    {
        if ( ! isset($options['writeConcern']) && isset($this->wc)) {
            $options['writeConcern'] = $this->wc;
        }

        $operation = new DeleteMany($this->dbname, $this->collname, $filter, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Deletes at most one document matching the filter.
     *
     * @see DeleteOne::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/delete/
     * @param array|object $filter  Query by which to delete documents
     * @param array        $options Command options
     * @return DeleteResult
     */
    public function deleteOne($filter, array $options = array())
    {
        if ( ! isset($options['writeConcern']) && isset($this->wc)) {
            $options['writeConcern'] = $this->wc;
        }

        $operation = new DeleteOne($this->dbname, $this->collname, $filter, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Finds the distinct values for a specified field across the collection.
     *
     * @see Distinct::__construct() for supported options
     * @param string $fieldName Field for which to return distinct values
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Command options
     * @return mixed[]
     */
    public function distinct($fieldName, $filter = array(), array $options = array())
    {
        $operation = new Distinct($this->dbname, $this->collname, $fieldName, $filter, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Drop this collection.
     *
     * @return object Command result document
     */
    public function drop()
    {
        $operation = new DropCollection($this->dbname, $this->collname);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Drop a single index in the collection.
     *
     * @param string $indexName Index name
     * @return object Command result document
     * @throws InvalidArgumentException if $indexName is an empty string or "*"
     */
    public function dropIndex($indexName)
    {
        $indexName = (string) $indexName;

        if ($indexName === '*') {
            throw new InvalidArgumentException('dropIndexes() must be used to drop multiple indexes');
        }

        $operation = new DropIndexes($this->dbname, $this->collname, $indexName);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Drop all indexes in the collection.
     *
     * @return object Command result document
     */
    public function dropIndexes()
    {
        $operation = new DropIndexes($this->dbname, $this->collname, '*');
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Finds documents matching the query.
     *
     * @see Find::__construct() for supported options
     * @see http://docs.mongodb.org/manual/core/read-operations-introduction/
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return Cursor
     */
    public function find($filter = array(), array $options = array())
    {
        $operation = new Find($this->dbname, $this->collname, $filter, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Finds a single document matching the query.
     *
     * @see FindOne::__construct() for supported options
     * @see http://docs.mongodb.org/manual/core/read-operations-introduction/
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Additional options
     * @return object|null
     */
    public function findOne($filter = array(), array $options = array())
    {
        $operation = new FindOne($this->dbname, $this->collname, $filter, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Finds a single document and deletes it, returning the original.
     *
     * The document to return may be null.
     *
     * @see FindOneAndDelete::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
     * @param array|object $filter  Query by which to filter documents
     * @param array        $options Command options
     * @return object|null
     */
    public function findOneAndDelete($filter, array $options = array())
    {
        $operation = new FindOneAndDelete($this->dbname, $this->collname, $filter, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Finds a single document and replaces it, returning either the original or
     * the replaced document.
     *
     * The document to return may be null. By default, the original document is
     * returned. Specify FindOneAndReplace::RETURN_DOCUMENT_AFTER for the
     * "returnDocument" option to return the updated document.
     *
     * @see FindOneAndReplace::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
     * @param array|object $filter      Query by which to filter documents
     * @param array|object $replacement Replacement document
     * @param array        $options     Command options
     * @return object|null
     */
    public function findOneAndReplace($filter, $replacement, array $options = array())
    {
        $operation = new FindOneAndReplace($this->dbname, $this->collname, $filter, $replacement, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Finds a single document and updates it, returning either the original or
     * the updated document.
     *
     * The document to return may be null. By default, the original document is
     * returned. Specify FindOneAndUpdate::RETURN_DOCUMENT_AFTER for the
     * "returnDocument" option to return the updated document.
     *
     * @see FindOneAndReplace::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
     * @param array|object $filter  Query by which to filter documents
     * @param array|object $update  Update to apply to the matched document
     * @param array        $options Command options
     * @return object|null
     */
    public function findOneAndUpdate($filter, $update, array $options = array())
    {
        $operation = new FindOneAndUpdate($this->dbname, $this->collname, $filter, $update, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Return the collection name.
     *
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collname;
    }

    /**
     * Return the database name.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->dbname;
    }

    /**
     * Return the collection namespace.
     *
     * @see http://docs.mongodb.org/manual/faq/developers/#faq-dev-namespace
     * @return string
     */
    public function getNamespace()
    {
        return $this->ns;
    }

    /**
     * Inserts multiple documents.
     *
     * @see InsertMany::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/insert/
     * @param array[]|object[] $documents The documents to insert
     * @param array            $options   Command options
     * @return InsertManyResult
     */
    public function insertMany(array $documents, array $options = array())
    {
        if ( ! isset($options['writeConcern']) && isset($this->wc)) {
            $options['writeConcern'] = $this->wc;
        }

        $operation = new InsertMany($this->dbname, $this->collname, $documents, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Inserts one document.
     *
     * @see InsertOne::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/insert/
     * @param array|object $document The document to insert
     * @param array        $options  Command options
     * @return InsertOneResult
     */
    public function insertOne($document, array $options = array())
    {
        if ( ! isset($options['writeConcern']) && isset($this->wc)) {
            $options['writeConcern'] = $this->wc;
        }

        $operation = new InsertOne($this->dbname, $this->collname, $document, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Returns information for all indexes for the collection.
     *
     * @see ListIndexes::__construct() for supported options
     * @return IndexInfoIterator
     */
    public function listIndexes(array $options = array())
    {
        $operation = new ListIndexes($this->dbname, $this->collname, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

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
    public function replaceOne($filter, $replacement, array $options = array())
    {
        if ( ! isset($options['writeConcern']) && isset($this->wc)) {
            $options['writeConcern'] = $this->wc;
        }

        $operation = new ReplaceOne($this->dbname, $this->collname, $filter, $replacement, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Updates all documents matching the filter.
     *
     * @see UpdateMany::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/update/
     * @param array|object $filter      Query by which to filter documents
     * @param array|object $replacement Update to apply to the matched documents
     * @param array        $options     Command options
     * @return UpdateResult
     */
    public function updateMany($filter, $update, array $options = array())
    {
        if ( ! isset($options['writeConcern']) && isset($this->wc)) {
            $options['writeConcern'] = $this->wc;
        }

        $operation = new UpdateMany($this->dbname, $this->collname, $filter, $update, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }

    /**
     * Updates at most one document matching the filter.
     *
     * @see ReplaceOne::__construct() for supported options
     * @see http://docs.mongodb.org/manual/reference/command/update/
     * @param array|object $filter      Query by which to filter documents
     * @param array|object $replacement Update to apply to the matched document
     * @param array        $options     Command options
     * @return UpdateResult
     */
    public function updateOne($filter, $update, array $options = array())
    {
        if ( ! isset($options['writeConcern']) && isset($this->wc)) {
            $options['writeConcern'] = $this->wc;
        }

        $operation = new UpdateOne($this->dbname, $this->collname, $filter, $update, $options);
        $server = $this->manager->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));

        return $operation->execute($server);
    }
}
