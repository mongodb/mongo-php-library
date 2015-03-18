<?php

namespace MongoDB;

use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Result;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\WriteConcern;

class Collection
{
    const VERSION = "0.1.0";

    /* {{{ consts & vars */
    const QUERY_FLAG_TAILABLE_CURSOR   = 0x02;
    const QUERY_FLAG_SLAVE_OKAY        = 0x04;
    const QUERY_FLAG_OPLOG_REPLY       = 0x08;
    const QUERY_FLAG_NO_CURSOR_TIMEOUT = 0x10;
    const QUERY_FLAG_AWAIT_DATA        = 0x20;
    const QUERY_FLAG_EXHAUST           = 0x40;
    const QUERY_FLAG_PARTIAL           = 0x80;


    const CURSOR_TYPE_NON_TAILABLE   = 0x00;
    const CURSOR_TYPE_TAILABLE       = self::QUERY_FLAG_TAILABLE_CURSOR;
    //self::QUERY_FLAG_TAILABLE_CURSOR | self::QUERY_FLAG_AWAIT_DATA;
    const CURSOR_TYPE_TAILABLE_AWAIT = 0x22;

    const FIND_ONE_AND_RETURN_BEFORE = 0x01;
    const FIND_ONE_AND_RETURN_AFTER  = 0x02;

    protected $manager;
    protected $ns;
    protected $wc;
    protected $rp;

    protected $dbname;
    protected $collname;
    /* }}} */


    /**
     * Constructs new Collection instance
     *
     * This is the suggested CRUD interface when using phongo.
     * It implements the MongoDB CRUD specification which is an interface all MongoDB
     * supported drivers follow.
     *
     * @param Manager        $manager The phongo Manager instance
     * @param string         $ns      Fully Qualified Namespace (dbname.collname)
     * @param WriteConcern   $wc      The WriteConcern to apply to writes
     * @param ReadPreference $rp      The ReadPreferences to apply to reads
     */
    public function __construct(Manager $manager, $ns, WriteConcern $wc = null, ReadPreference $rp = null)
    {
        $this->manager = $manager;
        $this->ns = $ns;
        $this->wc = $wc;
        $this->rp = $rp;

        list($this->dbname, $this->collname) = explode(".", $ns, 2);
    }

    /**
     * Runs an aggregation framework pipeline
     * NOTE: The return value of this method depends on your MongoDB server version
     *       and possibly options.
     *       MongoDB 2.6 (and later) will return a Cursor by default
     *       MongoDB pre 2.6 will return an ArrayIterator
     *
     * @see http://docs.mongodb.org/manual/reference/command/aggregate/
     * @see Collection::getAggregateOptions() for supported $options
     *
     * @param array $pipeline The pipeline to execute
     * @param array $options  Additional options
     * @return Iterator
     */
    public function aggregate(array $pipeline, array $options = array())
    {
        $options = array_merge($this->getAggregateOptions(), $options);
        $options = $this->_massageAggregateOptions($options);
        $cmd = array(
            "aggregate" => $this->collname,
            "pipeline"  => $pipeline,
        ) + $options;

        $result = $this->_runCommand($this->dbname, $cmd);
        $doc = $result->toArray();
        if (isset($cmd["cursor"]) && $cmd["cursor"]) {
            return $result;
        } else {
            if ($doc["ok"]) {
                return new \ArrayIterator($doc["result"]);
            }
        }

        throw $this->_generateCommandException($doc);
    }

    /**
     * Adds a full set of write operations into a bulk and executes it
     *
     * The syntax of the $bulk array is:
     *     $bulk = [
     *         [
     *             'METHOD' => [
     *                 $document,
     *                 $extraArgument1,
     *                 $extraArgument2,
     *             ],
     *         ],
     *         [
     *             'METHOD' => [
     *                 $document,
     *                 $extraArgument1,
     *                 $extraArgument2,
     *             ],
     *         ],
     *     ]
     *
     *
     * Where METHOD is one of
     *     - 'insertOne'
     *           Supports no $extraArgument
     *     - 'updateMany'
     *           Requires $extraArgument1, same as $update for Collection::updateMany()
     *           Optional $extraArgument2, same as $options for Collection::updateMany()
     *     - 'updateOne'
     *           Requires $extraArgument1, same as $update for Collection::updateOne()
     *           Optional $extraArgument2, same as $options for Collection::updateOne()
     *     - 'replaceOne'
     *           Requires $extraArgument1, same as $update for Collection::replaceOne()
     *           Optional $extraArgument2, same as $options for Collection::replaceOne()
     *     - 'deleteOne'
     *           Supports no $extraArgument
     *     - 'deleteMany'
     *           Supports no $extraArgument
     *
     * @example Collection-bulkWrite.php Using Collection::bulkWrite()
     *
     * @see Collection::getBulkOptions() for supported $options
     *
     * @param array $bulk    Array of operations
     * @param array $options Additional options
     * @return WriteResult
     */
    public function bulkWrite(array $bulk, array $options = array())
    {
        $options = array_merge($this->getBulkOptions(), $options);

        $bulk = new BulkWrite($options["ordered"]);

        foreach ($bulk as $n => $op) {
            foreach ($op as $opname => $args) {
                if (!isset($args[0])) {
                    throw new \InvalidArgumentException(sprintf("Missing argument#1 for '%s' (operation#%d)", $opname, $n));
                }

                switch ($opname) {
                case "insertOne":
                    $bulk->insert($args[0]);
                    break;

                case "updateMany":
                    if (!isset($args[1])) {
                        throw new \InvalidArgumentException(sprintf("Missing argument#2 for '%s' (operation#%d)", $opname, $n));
                    }
                    $options = array_merge($this->getWriteOptions(), isset($args[2]) ? $args[2] : array(), array("limit" => 0));

                    $bulk->update($args[0], $args[1], $options);
                    break;

                case "updateOne":
                    if (!isset($args[1])) {
                        throw new \InvalidArgumentException(sprintf("Missing argument#2 for '%s' (operation#%d)", $opname, $n));
                    }
                    $options = array_merge($this->getWriteOptions(), isset($args[2]) ? $args[2] : array(), array("limit" => 1));
                    if (key($args[1])[0] != '$') {
                        throw new \InvalidArgumentException("First key in \$update must be a \$operator");
                    }

                    $bulk->update($args[0], $args[1], $options);
                    break;

                case "replaceOne":
                    if (!isset($args[1])) {
                        throw new \InvalidArgumentException(sprintf("Missing argument#2 for '%s' (operation#%d)", $opname, $n));
                    }
                    $options = array_merge($this->getWriteOptions(), isset($args[2]) ? $args[2] : array(), array("limit" => 1));
                    if (key($args[1])[0] == '$') {
                        throw new \InvalidArgumentException("First key in \$update must NOT be a \$operator");
                    }

                    $bulk->update($args[0], $args[1], $options);
                    break;

                case "deleteOne":
                    $options = array_merge($this->getWriteOptions(), isset($args[1]) ? $args[1] : array(), array("limit" => 1));
                    $bulk->delete($args[0], $options);
                    break;

                case "deleteMany":
                    $options = array_merge($this->getWriteOptions(), isset($args[1]) ? $args[1] : array(), array("limit" => 0));
                    $bulk->delete($args[0], $options);
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf("Unknown operation type called '%s' (operation#%d)", $opname, $n));
                }
            }
        }
        return $this->manager->executeBulkWrite($this->ns, $bulk, $this->wc);
    }

    /**
     * Counts all documents matching $filter
     * If no $filter provided, returns the numbers of documents in the collection
     *
     * @see http://docs.mongodb.org/manual/reference/command/count/
     * @see Collection::getCountOptions() for supported $options
     *
     * @param array $filter   The find query to execute
     * @param array $options  Additional options
     * @return integer
     */
    public function count(array $filter = array(), array $options = array())
    {
        $cmd = array(
            "count" => $this->collname,
            "query" => $filter,
        ) + $options;

        $doc = $this->_runCommand($this->dbname, $cmd)->toArray();
        if ($doc["ok"]) {
            return $doc["n"];
        }
        throw $this->_generateCommandException($doc);
    }

    /**
     * Create a single index in the collection.
     *
     * @see http://docs.mongodb.org/manual/reference/command/createIndexes/
     * @see http://docs.mongodb.org/manual/reference/method/db.collection.createIndex/
     * @param array|object $keys
     * @param array        $options
     * @return string The name of the created index
     */
    public function createIndex($keys, array $options = array())
    {
        // TODO
    }

    /**
     * Create multiple indexes in the collection.
     *
     * TODO: decide if $models should be an array of associative arrays, using
     * createIndex()'s parameter names as keys, or tuples, using parameters in
     * order (e.g. [keys, options]).
     *
     * @see http://docs.mongodb.org/manual/reference/command/createIndexes/
     * @see http://docs.mongodb.org/manual/reference/method/db.collection.createIndex/
     * @param array $models
     * @return string[] The names of the created indexes
     */
    public function createIndexes(array $models)
    {
        // TODO
    }

    /**
     * Deletes a document matching the $filter criteria.
     * NOTE: Will delete ALL documents matching $filter
     *
     * @see http://docs.mongodb.org/manual/reference/command/delete/
     *
     * @param array $filter The $filter criteria to delete
     * @return DeleteResult
     */
    public function deleteMany(array $filter)
    {
        $wr = $this->_delete($filter, 0);

        return new DeleteResult($wr);
    }

    /**
     * Deletes a document matching the $filter criteria.
     * NOTE: Will delete at most ONE document matching $filter
     *
     * @see http://docs.mongodb.org/manual/reference/command/delete/
     *
     * @param array $filter The $filter criteria to delete
     * @return DeleteResult
     */
    public function deleteOne(array $filter)
    {
        $wr = $this->_delete($filter);

        return new DeleteResult($wr);
    }

    /**
     * Finds the distinct values for a specified field across the collection
     *
     * @see http://docs.mongodb.org/manual/reference/command/distinct/
     * @see Collection::getDistinctOptions() for supported $options
     *
     * @param string $fieldName  The fieldname to use
     * @param array $filter      The find query to execute
     * @param array $options     Additional options
     * @return integer
     */
    public function distinct($fieldName, array $filter = array(), array $options = array())
    {
        $options = array_merge($this->getDistinctOptions(), $options);
        $cmd = array(
            "distinct" => $this->collname,
            "key"      => $fieldName,
            "query"    => $filter,
        ) + $options;

        $doc = $this->_runCommand($this->dbname, $cmd)->toArray();
        if ($doc["ok"]) {
            return $doc["values"];
        }
        throw $this->_generateCommandException($doc);
    }

    /**
     * Drop this collection.
     *
     * @return Result
     */
    public function drop()
    {
        // TODO
    }

    /**
     * Drop a single index in the collection.
     *
     * @see http://docs.mongodb.org/manual/reference/command/dropIndexes/
     * @see http://docs.mongodb.org/manual/reference/method/db.collection.dropIndex/
     * @param string $indexName
     * @return Result
     * @throws InvalidArgumentException if "*" is specified
     */
    public function dropIndex($indexName)
    {
        // TODO
    }

    /**
     * Drop all indexes in the collection.
     *
     * @see http://docs.mongodb.org/manual/reference/command/dropIndexes/
     * @see http://docs.mongodb.org/manual/reference/method/db.collection.dropIndexes/
     * @return Result
     */
    public function dropIndexes()
    {
        // TODO
    }

    /**
     * Performs a find (query) on the collection
     *
     * @see http://docs.mongodb.org/manual/core/read-operations-introduction/
     * @see Collection::getFindOptions() for supported $options
     *
     * @param array $filter    The find query to execute
     * @param array $options   Additional options
     * @return Result
     */
    public function find(array $filter = array(), array $options = array())
    {
        $options = array_merge($this->getFindOptions(), $options);

        $query = $this->_buildQuery($filter, $options);

        $cursor = $this->manager->executeQuery($this->ns, $query, $this->rp);

        return $cursor;
    }

    /**
     * Performs a find (query) on the collection, returning at most one result
     *
     * @see http://docs.mongodb.org/manual/core/read-operations-introduction/
     * @see Collection::getFindOptions() for supported $options
     *
     * @param array $filter    The find query to execute
     * @param array $options   Additional options
     * @return array|false     The matched document, or false on failure
     */
    public function findOne(array $filter = array(), array $options = array())
    {
        $options = array_merge($this->getFindOptions(), array("limit" => 1), $options);

        $query = $this->_buildQuery($filter, $options);

        $cursor = $this->manager->executeQuery($this->ns, $query, $this->rp);

        $array = iterator_to_array($cursor);
        if ($array) {
            return $array[0];
        }

        return false;
    }

    /**
     * Finds a single document and deletes it, returning the original.
     *
     * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
     * @see Collection::getFindOneAndDelete() for supported $options
     *
     * @param array $filter   The $filter criteria to search for
     * @param array $options  Additional options
     * @return array The original document
     */
    public function findOneAndDelete(array $filter, array $options = array())
    {
        $options = array_merge($this->getFindOneAndDeleteOptions(), $options);
        $options = $this->_massageFindAndModifyOptions($options);
        $cmd = array(
            "findandmodify" => $this->collname,
            "query"         => $filter,
        ) + $options;

        $doc = $this->_runCommand($this->dbname, $cmd)->toArray();
        if ($doc["ok"]) {
            return $doc["value"];
        }

        throw $this->_generateCommandException($doc);
    }

    /**
     * Finds a single document and replaces it, returning either the original or the replaced document
     * By default, returns the original document.
     * To return the new document set:
     *     $options = array("returnDocument" => Collection::FIND_ONE_AND_RETURN_AFTER);
     *
     * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
     * @see Collection::getFindOneAndReplace() for supported $options
     *
     * @param array $filter       The $filter criteria to search for
     * @param array $replacement  The document to replace with
     * @param array $options      Additional options
     * @return array
     */
    public function findOneAndReplace(array $filter, array $replacement, array $options = array())
    {
        if (key($replacement)[0] == '$') {
            throw new \InvalidArgumentException("First key in \$replacement must NOT be a \$operator");
        }

        $options = array_merge($this->getFindOneAndReplaceOptions(), $options);
        $options = $this->_massageFindAndModifyOptions($options, $replacement);

        $cmd = array(
            "findandmodify" => $this->collname,
            "query"         => $filter,
        ) + $options;

        $doc = $this->_runCommand($this->dbname, $cmd)->toArray();
        if ($doc["ok"]) {
            return $doc["value"];
        }

        throw $this->_generateCommandException($doc);
    }

    /**
     * Finds a single document and updates it, returning either the original or the updated document
     * By default, returns the original document.
     * To return the new document set:
     *     $options = array("returnDocument" => Collection::FIND_ONE_AND_RETURN_AFTER);
     *
     *
     * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
     * @see Collection::getFindOneAndUpdate() for supported $options
     *
     * @param array $filter   The $filter criteria to search for
     * @param array $update   An array of update operators to apply to the document
     * @param array $options  Additional options
     * @return array
     */
    public function findOneAndUpdate(array $filter, array $update, array $options = array())
    {
        if (key($update)[0] != '$') {
            throw new \InvalidArgumentException("First key in \$update must be a \$operator");
        }

        $options = array_merge($this->getFindOneAndUpdateOptions(), $options);
        $options = $this->_massageFindAndModifyOptions($options, $update);

        $cmd = array(
            "findandmodify" => $this->collname,
            "query"         => $filter,
        ) + $options;

        $doc = $this->_runCommand($this->dbname, $cmd)->toArray();
        if ($doc["ok"]) {
            return $doc["value"];
        }

        throw $this->_generateCommandException($doc);
    }

    /**
     * Retrieves all aggregate options with their default values.
     *
     * @return array of Collection::aggregate() options
     */
    public function getAggregateOptions()
    {
        $opts = array(
            /**
             * Enables writing to temporary files. When set to true, aggregation stages
             * can write data to the _tmp subdirectory in the dbPath directory. The
             * default is false.
             *
             * @see http://docs.mongodb.org/manual/reference/command/aggregate/
             */
            "allowDiskUse" => false,

            /**
             * The number of documents to return per batch.
             *
             * @see http://docs.mongodb.org/manual/reference/command/aggregate/
             */
            "batchSize" => 0,

            /**
             * The maximum amount of time to allow the query to run.
             *
             * @see http://docs.mongodb.org/manual/reference/command/aggregate/
             */
            "maxTimeMS" => 0,

            /**
             * Indicates if the results should be provided as a cursor.
             *
             * The default for this value depends on the version of the server.
             * - Servers >= 2.6 will use a default of true.
             * - Servers < 2.6 will use a default of false.
             *
             * As with any other property, this value can be changed.
             *
             * @see http://docs.mongodb.org/manual/reference/command/aggregate/
             */
            "useCursor" => true,
        );

        /* FIXME: Add a version check for useCursor */
        return $opts;
    }

    /**
     * Retrieves all Bulk Write options with their default values.
     *
     * @return array of available Bulk Write options
     */
    public function getBulkOptions()
    {
        return array(
            "ordered" => false,
        );
    }

    /**
     * Returns the CollectionName this object operates on
     *
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collname;
    }

    /**
     * Retrieves all count options with their default values.
     *
     * @return array of Collection::count() options
     */
    public function getCountOptions()
    {
        return array(
            /**
             * The index to use.
             *
             * @see http://docs.mongodb.org/manual/reference/command/count/
             */
            "hint" => "", // string or document

            /**
             * The maximum number of documents to count.
             *
             * @see http://docs.mongodb.org/manual/reference/command/count/
             */
            "limit" => 0,

            /**
             * The maximum amount of time to allow the query to run.
             *
             * @see http://docs.mongodb.org/manual/reference/command/count/
             */
            "maxTimeMS" => 0,

            /**
             * The number of documents to skip before returning the documents.
             *
             * @see http://docs.mongodb.org/manual/reference/command/count/
             */
            "skip"  => 0,
        );
    }

    /**
     * Returns the DatabaseName this object operates on
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->dbname;
    }

    /**
     * Retrieves all distinct options with their default values.
     *
     * @return array of Collection::distinct() options
     */
    public function getDistinctOptions()
    {
        return array(
            /**
             * The maximum amount of time to allow the query to run. The default is infinite.
             *
             * @see http://docs.mongodb.org/manual/reference/command/distinct/
             */
            "maxTimeMS" => 0,
        );
    }

    /**
     * Retrieves all findOneDelete options with their default values.
     *
     * @return array of Collection::findOneAndDelete() options
     */
    public function getFindOneAndDeleteOptions()
    {
        return array(

            /**
             * The maximum amount of time to allow the query to run.
             *
             * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
             */
            "maxTimeMS" => 0,

            /**
             * Limits the fields to return for all matching documents.
             *
             * @see http://docs.mongodb.org/manual/tutorial/project-fields-from-query-results
             */
            "projection" => array(),

            /**
             * Determines which document the operation modifies if the query selects multiple documents.
             *
             * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
             */
            "sort" => array(),
        );
    }

    /**
     * Retrieves all findOneAndReplace options with their default values.
     *
     * @return array of Collection::findOneAndReplace() options
     */
    public function getFindOneAndReplaceOptions()
    {
        return array(

            /**
             * The maximum amount of time to allow the query to run.
             *
             * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
             */
            "maxTimeMS" => 0,

            /**
             * Limits the fields to return for all matching documents.
             *
             * @see http://docs.mongodb.org/manual/tutorial/project-fields-from-query-results
             */
            "projection" => array(),

            /**
             * When ReturnDocument.After, returns the replaced or inserted document rather than the original.
             * Defaults to ReturnDocument.Before.
             *
             * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
             */
            "returnDocument" => self::FIND_ONE_AND_RETURN_BEFORE,

            /**
             * Determines which document the operation modifies if the query selects multiple documents.
             *
             * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
             */
            "sort" => array(),

            /**
             * When true, findAndModify creates a new document if no document matches the query. The
             * default is false.
             *
             * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
             */
            "upsert" => false,
        );
    }

    /**
     * Retrieves all findOneAndUpdate options with their default values.
     *
     * @return array of Collection::findOneAndUpdate() options
     */
    public function getFindOneAndUpdateOptions()
    {
        return array(

            /**
             * The maximum amount of time to allow the query to run.
             *
             * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
             */
            "maxTimeMS" => 0,

            /**
             * Limits the fields to return for all matching documents.
             *
             * @see http://docs.mongodb.org/manual/tutorial/project-fields-from-query-results
             */
            "projection" => array(),

            /**
             * When ReturnDocument.After, returns the updated or inserted document rather than the original.
             * Defaults to ReturnDocument.Before.
             *
             * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
             */
            "returnDocument" => self::FIND_ONE_AND_RETURN_BEFORE,

            /**
             * Determines which document the operation modifies if the query selects multiple documents.
             *
             * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
             */
            "sort" => array(),

            /**
             * When true, creates a new document if no document matches the query. The default is false.
             *
             * @see http://docs.mongodb.org/manual/reference/command/findAndModify/
             */
            "upsert" => false,
        );
    }

    /**
     * Retrieves all find options with their default values.
     *
     * @return array of Collection::find() options
     */
    public function getFindOptions()
    {
        return array(
            /**
             * Get partial results from a mongos if some shards are down (instead of throwing an error).
             *
             * @see http://docs.mongodb.org/meta-driver/latest/legacy/mongodb-wire-protocol/#op-query
             */
            "allowPartialResults" => false,

            /**
             * The number of documents to return per batch.
             *
             * @see http://docs.mongodb.org/manual/reference/method/cursor.batchSize/
             */
            "batchSize" => 101,

            /**
             * Attaches a comment to the query. If $comment also exists
             * in the modifiers document, the comment field overwrites $comment.
             *
             * @see http://docs.mongodb.org/manual/reference/operator/meta/comment/
             */
            "comment" => "",

            /**
             * Indicates the type of cursor to use. This value includes both
             * the tailable and awaitData options.
             * The default is Collection::CURSOR_TYPE_NON_TAILABLE.
             *
             * @see http://docs.mongodb.org/manual/reference/operator/meta/comment/
             */
            "cursorType" => self::CURSOR_TYPE_NON_TAILABLE,

            /**
             * The maximum number of documents to return.
             *
             * @see http://docs.mongodb.org/manual/reference/method/cursor.limit/
             */
            "limit" => 0,

            /**
             * The maximum amount of time to allow the query to run. If $maxTimeMS also exists
             * in the modifiers document, the maxTimeMS field overwrites $maxTimeMS.
             *
             * @see http://docs.mongodb.org/manual/reference/operator/meta/maxTimeMS/
             */
            "maxTimeMS" => 0,

            /**
             * Meta-operators modifying the output or behavior of a query.
             *
             * @see http://docs.mongodb.org/manual/reference/operator/query-modifier/
             */
            "modifiers" => array(),

            /**
             * The server normally times out idle cursors after an inactivity period (10 minutes)
             * to prevent excess memory use. Set this option to prevent that.
             *
             * @see http://docs.mongodb.org/meta-driver/latest/legacy/mongodb-wire-protocol/#op-query
             */
            "noCursorTimeout" => false,

            /**
             * Internal replication use only - driver should not set
             *
             * @see http://docs.mongodb.org/meta-driver/latest/legacy/mongodb-wire-protocol/#op-query
             * @internal
             */
            "oplogReplay" => false,

            /**
             * Limits the fields to return for all matching documents.
             *
             * @see http://docs.mongodb.org/manual/tutorial/project-fields-from-query-results/
             */
            "projection" => array(),

            /**
             * The number of documents to skip before returning.
             *
             * @see http://docs.mongodb.org/manual/reference/method/cursor.skip/
             */
            "skip" => 0,

            /**
             * The order in which to return matching documents. If $orderby also exists
             * in the modifiers document, the sort field overwrites $orderby.
             *
             * @see http://docs.mongodb.org/manual/reference/method/cursor.sort/
             */
            "sort" => array(),
        );
    }

    /**
     * Retrieves all Write options with their default values.
     *
     * @return array of available Write options
     */
    public function getWriteOptions()
    {
        return array(
            "ordered" => false,
            "upsert"  => false,
            "limit"   => 1,
        );
    }

    /**
     * Inserts the provided documents
     *
     * @see http://docs.mongodb.org/manual/reference/command/insert/
     *
     * @param array $documents The documents to insert
     * @return InsertManyResult
     */
    public function insertMany(array $documents)
    {
        $options = array_merge($this->getWriteOptions());

        $bulk = new BulkWrite($options["ordered"]);
        $insertedIds = array();

        foreach ($documents as $i => $document) {
            $insertedId = $bulk->insert($document);

            if ($insertedId !== null) {
                $insertedIds[$i] = $insertedId;
            }
        }

        $writeResult = $this->manager->executeBulkWrite($this->ns, $bulk, $this->wc);

        return new InsertManyResult($writeResult, $insertedIds);
    }

    /**
     * Inserts the provided document
     *
     * @see http://docs.mongodb.org/manual/reference/command/insert/
     *
     * @param array $document  The document to insert
     * @param array $options   Additional options
     * @return InsertOneResult
     */
    public function insertOne(array $document)
    {
        $options = array_merge($this->getWriteOptions());

        $bulk = new BulkWrite($options["ordered"]);
        $id    = $bulk->insert($document);
        $wr    = $this->manager->executeBulkWrite($this->ns, $bulk, $this->wc);

        return new InsertOneResult($wr, $id);
    }

    /**
     * Replace one document
     *
     * @see http://docs.mongodb.org/manual/reference/command/update/
     * @see Collection::getWriteOptions() for supported $options
     *
     * @param array $filter   The document to be replaced
     * @param array $update   The document to replace with
     * @param array $options  Additional options
     * @return UpdateResult
     */
    public function replaceOne(array $filter, array $update, array $options = array())
    {
        if (key($update)[0] == '$') {
            throw new \InvalidArgumentException("First key in \$update must NOT be a \$operator");
        }
        $wr = $this->_update($filter, $update, $options);

        return new UpdateResult($wr);
    }

    /**
     * Update one document
     * NOTE: Will update ALL documents matching $filter
     *
     * @see http://docs.mongodb.org/manual/reference/command/update/
     * @see Collection::getWriteOptions() for supported $options
     *
     * @param array $filter   The document to be replaced
     * @param array $update   An array of update operators to apply to the document
     * @param array $options  Additional options
     * @return UpdateResult
     */
    public function updateMany(array $filter, $update, array $options = array())
    {
        $wr = $this->_update($filter, $update, $options + array("limit" => 0));

        return new UpdateResult($wr);
    }

    /**
     * Update one document
     * NOTE: Will update at most ONE document matching $filter
     *
     * @see http://docs.mongodb.org/manual/reference/command/update/
     * @see Collection::getWriteOptions() for supported $options
     *
     * @param array $filter   The document to be replaced
     * @param array $update   An array of update operators to apply to the document
     * @param array $options  Additional options
     * @return UpdateResult
     */
    public function updateOne(array $filter, array $update, array $options = array())
    {
        if (key($update)[0] != '$') {
            throw new \InvalidArgumentException("First key in \$update must be a \$operator");
        }
        $wr = $this->_update($filter, $update, $options);

        return new UpdateResult($wr);
    }

    /**
     * Helper to build a Query object
     *
     * @param array $filter the query document
     * @param array $options query/protocol options
     * @return Query
     * @internal
     */
    final protected function _buildQuery($filter, $options)
    {
        if ($options["comment"]) {
            $options["modifiers"]['$comment'] = $options["comment"];
        }
        if ($options["maxTimeMS"]) {
            $options["modifiers"]['$maxTimeMS'] = $options["maxTimeMS"];
        }
        if ($options["sort"]) {
            $options['$orderby'] = $options["sort"];
        }

        $flags = $this->_opQueryFlags($options);
        $options["cursorFlags"] = $flags;


        $query = new Query($filter, $options);

        return $query;
    }

    /**
     * Internal helper for delete one/many documents
     * @internal
     */
    final protected function _delete($filter, $limit = 1)
    {
        $options = array_merge($this->getWriteOptions(), array("limit" => $limit));

        $bulk  = new BulkWrite($options["ordered"]);
        $bulk->delete($filter, $options);
        return $this->manager->executeBulkWrite($this->ns, $bulk, $this->wc);
    }

    /**
     * Internal helper for throwing an exception with error message
     * @internal
     */
    final protected function _generateCommandException($doc)
    {
        if ($doc["errmsg"]) {
            return new Exception($doc["errmsg"]);
        }
        var_dump($doc);
        return new \Exception("FIXME: Unknown error");
    }

    /**
     * Internal helper for massaging aggregate options
     * @internal
     */
    protected function _massageAggregateOptions($options)
    {
        if ($options["useCursor"]) {
            $options["cursor"] = array("batchSize" => $options["batchSize"]);
        }
        unset($options["useCursor"], $options["batchSize"]);

        return $options;
    }

    /**
     * Constructs the Query Wire Protocol field 'flags' based on $options
     * provided to other helpers
     *
     * @param array $options
     * @return integer OP_QUERY Wire Protocol flags
     * @internal
     */
    final protected function _opQueryFlags($options)
    {
        $flags = 0;

        $flags |= $options["allowPartialResults"] ? self::QUERY_FLAG_PARTIAL : 0;
        $flags |= $options["cursorType"] ? $options["cursorType"] : 0;
        $flags |= $options["oplogReplay"] ? self::QUERY_FLAG_OPLOG_REPLY: 0;
        $flags |= $options["noCursorTimeout"] ? self::QUERY_FLAG_NO_CURSOR_TIMEOUT : 0;

        return $flags;
    }

    /**
     * Internal helper for running a command
     * @internal
     */
    final protected function _runCommand($dbname, array $cmd, ReadPreference $rp = null)
    {
        //var_dump(\BSON\toJSON(\BSON\fromArray($cmd)));
        $command = new Command($cmd);
        return $this->manager->executeCommand($dbname, $command, $rp);
    }

    /**
     * Internal helper for replacing/updating one/many documents
     * @internal
     */
    protected function _update($filter, $update, $options)
    {
        $options = array_merge($this->getWriteOptions(), $options);

        $bulk  = new BulkWrite($options["ordered"]);
        $bulk->update($filter, $update, $options);
        return $this->manager->executeBulkWrite($this->ns, $bulk, $this->wc);
    }
}
