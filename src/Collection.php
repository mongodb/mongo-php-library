<?php
namespace MongoDB;

/* {{{ phongo includes */
use MongoDB\Manager;
use MongoDB\Query;
use MongoDB\Command;
use MongoDB\ReadPreference;
use MongoDB\WriteBatch;
/* }}} */

class Collection {
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
    protected $rp;
    protected $wc;
    protected $ns;

    protected $dbname;
    protected $collname;
    /* }}} */


    function __construct(Manager $manager, $ns, WriteConcern $wc = null, ReadPreference $rp = null) { /* {{{ */
        $this->manager = $manager;
        $this->ns = $ns;
        $this->wc = $wc;
        $this->rp = $rp;
        list($this->dbname, $this->collname) = explode(".", $ns, 2);
    } /* }}} */

    function find(array $filter = array(), array $options = array()) { /* {{{ {{{ */
        $options = array_merge($this->getFindOptions(), $options);

        $query = $this->_buildQuery($filter, $options);

        $cursor = $this->manager->executeQuery($this->ns, $query, $this->rp);

        return $cursor;
    } /* }}} */
    function getFindOptions() { /* {{{ */
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
             * The default is MongoDB\self::CURSOR_TYPE_NON_TAILABLE.
             *
             * @see MongoDB\CursorType
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
    } /* }}} */
    protected function _opQueryFlags($options) { /* {{{ */
        $flags = 0;

        $flags |= $options["allowPartialResults"] ? self::QUERY_FLAG_PARTIAL : 0;
        $flags |= $options["cursorType"] ? $options["cursorType"] : 0;
        $flags |= $options["oplogReplay"] ? self::QUERY_FLAG_OPLOG_REPLY: 0;
        $flags |= $options["noCursorTimeout"] ? self::QUERY_FLAG_NO_CURSOR_TIMEOUT : 0;

        return $flags;
    } /* }}} */
    protected function _buildQuery($filter, $options) { /* {{{ */
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
    } /* }}} */
    /* }}} */
    /* {{{ writes */
    function getWriteOptions() { /* {{{ */
        return array(
            "ordered" => false,
            "upsert"  => false,
            "limit"   => 1,
        );
    } /* }}} */
    function getBulkOptions() { /* {{{ */
        return array(
            "ordered" => false,
        );
    } /* }}} */

    function bulkWrite(array $bulk, array $options = array()) { /* {{{ */
        $options = array_merge($this->getBulkOptions(), $options);

        $batch = new WriteBatch($options["ordered"]);

        foreach($bulk as $n => $op) {
            foreach($op as $opname => $args) {
                if (!isset($args[0])) {
                    throw new \InvalidArgumentException(sprintf("Missing argument#1 for '%s' (operation#%d)", $opname, $n));
                }

                switch($opname) {
                case "insertOne":
                    $batch->insert($args[0]);
                    break;

                case "updateMany":
                    if (!isset($args[1])) {
                        throw new \InvalidArgumentException(sprintf("Missing argument#2 for '%s' (operation#%d)", $opname, $n));
                    }
                    $options = array_merge($this->getWriteOptions(), isset($args[2]) ? $args[2] : array(), array("limit" => 0));

                    $batch->update($args[0], $args[1], $options);
                    break;

                case "updateOne":
                    if (!isset($args[1])) {
                        throw new \InvalidArgumentException(sprintf("Missing argument#2 for '%s' (operation#%d)", $opname, $n));
                    }
                    $options = array_merge($this->getWriteOptions(), isset($args[2]) ? $args[2] : array(), array("limit" => 1));
                    if (key($args[1])[0] != '$') {
                        throw new \InvalidArgumentException("First key in \$update must be a \$operator");
                    }

                    $batch->update($args[0], $args[1], $options);
                    break;

                case "replaceOne":
                    if (!isset($args[1])) {
                        throw new \InvalidArgumentException(sprintf("Missing argument#2 for '%s' (operation#%d)", $opname, $n));
                    }
                    $options = array_merge($this->getWriteOptions(), isset($args[2]) ? $args[2] : array(), array("limit" => 1));
                    if (key($args[1])[0] == '$') {
                        throw new \InvalidArgumentException("First key in \$update must NOT be a \$operator");
                    }

                    $batch->update($args[0], $args[1], $options);
                    break;

                case "deleteOne":
                    $options = array_merge($this->getWriteOptions(), isset($args[1]) ? $args[1] : array(), array("limit" => 1));
                    $batch->delete($args[0], $options);
                    break;

                case "deleteMany":
                    $options = array_merge($this->getWriteOptions(), isset($args[1]) ? $args[1] : array(), array("limit" => 0));
                    $batch->delete($args[0], $options);
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf("Unknown operation type called '%s' (operation#%d)", $opname, $n));
                }
            }
        }
        return $this->manager->executeWriteBatch($this->ns, $batch, $this->wc);
    } /* }}} */

    function insertOne(array $filter) { /* {{{ */
        $options = array_merge($this->getWriteOptions());

        $batch = new WriteBatch($options["ordered"]);
        $id    = $batch->insert($filter);
        $wr    = $this->manager->executeWriteBatch($this->ns, $batch, $this->wc);

        return new InsertResult($wr, $id);
    } /* }}} */

    protected function _delete($filter, $limit = 1) { /* {{{ */
        $options = array_merge($this->getWriteOptions(), array("limit" => $limit));

        $batch  = new WriteBatch($options["ordered"]);
        $batch->delete($filter, $options);
        return $this->manager->executeWriteBatch($this->ns, $batch, $this->wc);
    } /* }}} */
    function deleteOne(array $filter) { /* {{{ */
        $wr = $this->_delete($filter);

        return new DeleteResult($wr);
    } /* }}} */
    function deleteMany(array $filter) { /* {{{ */
        $wr = $this->_delete($filter, 0);

        return new DeleteResult($wr);
    } /* }}} */

    protected function _update($filter, $update, $options) { /* {{{ */
        $options = array_merge($this->getWriteOptions(), $options);

        $batch  = new WriteBatch($options["ordered"]);
        $batch->update($filter, $update, $options);
        return $this->manager->executeWriteBatch($this->ns, $batch, $this->wc);
    } /* }}} */
    function replaceOne(array $filter, array $update, array $options = array()) { /* {{{ */
        if (key($update)[0] == '$') {
            throw new \InvalidArgumentException("First key in \$update must NOT be a \$operator");
        }
        $wr = $this->_update($filter, $update, $options);

        return new UpdateResult($wr);
    } /* }}} */
    function updateOne(array $filter, array $update, array $options = array()) { /* {{{ */
        if (key($update)[0] != '$') {
            throw new \InvalidArgumentException("First key in \$update must be a \$operator");
        }
        $wr = $this->_update($filter, $update, $options);

        return new UpdateResult($wr);
    } /* }}} */
    function updateMany(array $filter, $update, array $options = array()) { /* {{{ */
        $wr = $this->_update($filter, $update, $options + array("limit" => 0));

        return new UpdateResult($wr);
    } /* }}} */
    /* }}} */

    function count(array $filter = array(), array $options = array()) { /* {{{ */
        $options = array_merge($this->getFindOptions(), $options);
        $cmd = array(
            "count" => $this->collname,
            "query" => $filter,
        ) + $options;

        $doc = $this->_runCommand($this->dbname, $cmd)->getResponseDocument();
        if ($doc["ok"]) {
            return $doc["n"];
        }
        throw $this->_generateCommandException($doc);
    } /* }}} */
    function getCountOptions() { /* {{{ */
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
    } /* }}} */

    function distinct($fieldName, array $filter = array(), array $options = array()) { /* {{{ */
        $options = array_merge($this->getDistinctOptions(), $options);
        $cmd = array(
            "distinct" => $this->collname,
            "key"      => $fieldName,
            "query"    => $filter,
        ) + $options;

        $doc = $this->_runCommand($this->dbname, $cmd)->getResponseDocument();
        if ($doc["ok"]) {
            return $doc["values"];
        }
        throw $this->_generateCommandException($doc);
    } /* }}} */
    function getDistinctOptions() { /* {{{ */
        return array(
            /**
             * The maximum amount of time to allow the query to run. The default is infinite.
             *
             * @see http://docs.mongodb.org/manual/reference/command/distinct/
             */
            "maxTimeMS" => 0,
        );
    } /* }}} */

    function aggregate(array $pipeline, array $options = array()) { /* {{{ */
        $options = array_merge($this->getAggregateOptions(), $options);
        $options = $this->_massageAggregateOptions($options);
        $cmd = array(
            "aggregate" => $this->collname,
            "pipeline"  => $pipeline,
        ) + $options;

        $result = $this->_runCommand($this->dbname, $cmd);
        $doc = $result->getResponseDocument();
        if (isset($cmd["cursor"]) && $cmd["cursor"]) {
            return $result;
        } else {
            if ($doc["ok"]) {
                return new \ArrayIterator($doc["result"]);
            }
        }

        throw $this->_generateCommandException($doc);
    } /* }}} */
    function getAggregateOptions() { /* {{{ */
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
    } /* }}} */
    protected function _massageAggregateOptions($options) { /* {{{ */
        if ($options["useCursor"]) {
            $options["cursor"] = array("batchSize" => $options["batchSize"]);
        }
        unset($options["useCursor"], $options["batchSize"]);

        return $options;
    } /* }}} */

    /* {{{ findAndModify */
    function findOneAndDelete(array $filter, array $options = array()) { /* {{{ */
        $options = array_merge($this->getFindOneAndDeleteOptions(), $options);
        $options = $this->_massageFindAndModifyOptions($options);
        $cmd = array(
            "findandmodify" => $this->collname,
            "query"         => $filter,
        ) + $options;

        $doc = $this->_runCommand($this->dbname, $cmd)->getResponseDocument();
        if ($doc["ok"]) {
            return $doc["value"];
        }

        throw $this->_generateCommandException($doc);
    } /* }}} */
    function getFindOneAndDeleteOptions() { /* {{{ */
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
    } /* }}} */

    function findOneAndReplace(array $filter, array $replacement, array $options = array()) { /* {{{ */
        if (key($replacement)[0] == '$') {
            throw new \InvalidArgumentException("First key in \$replacement must NOT be a \$operator");
        }

        $options = array_merge($this->getFindOneAndReplaceOptions(), $options);
        $options = $this->_massageFindAndModifyOptions($options, $replacement);

        $cmd = array(
            "findandmodify" => $this->collname,
            "query"         => $filter,
        ) + $options;

        $doc = $this->_runCommand($this->dbname, $cmd)->getResponseDocument();
        if ($doc["ok"]) {
            return $doc["value"];
        }

        throw $this->_generateCommandException($doc);
    } /* }}} */
    function getFindOneAndReplaceOptions() { /* {{{ */
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

    } /* }}} */

    function findOneAndUpdate(array $filter, array $update, array $options = array()) { /* {{{ */
        if (key($update)[0] != '$') {
            throw new \InvalidArgumentException("First key in \$update must be a \$operator");
        }

        $options = array_merge($this->getFindOneAndUpdateOptions(), $options);
        $options = $this->_massageFindAndModifyOptions($options, $update);

        $cmd = array(
            "findandmodify" => $this->collname,
            "query"         => $filter,
        ) + $options;

        $doc = $this->_runCommand($this->dbname, $cmd)->getResponseDocument();
        if ($doc["ok"]) {
            return $doc["value"];
        }

        throw $this->_generateCommandException($doc);
    } /* }}} */
    function getFindOneAndUpdateOptions() { /* {{{ */
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

    } /* }}} */

    protected function _massageFindAndModifyOptions($options, $update = array()) { /* {{{ */
        $ret = array(
            "sort"   => $options["sort"],
            "new"    => isset($options["returnDocument"]) ? $options["returnDocument"] == self::FIND_ONE_AND_RETURN_AFTER : false,
            "fields" => $options["projection"],
            "upsert" => isset($options["upsert"]) ? $options["upsert"] : false,
        );
        if ($update) {
            $ret["update"] = $update;
        } else {
            $ret["remove"] = true;
        }

        return $ret;
    } /* }}} */
    /* }}} */

    protected function _generateCommandException($doc) { /* {{{ */
        if ($doc["errmsg"]) {
            return new Exception($doc["errmsg"]);
        }
        var_dump($doc);
        return new \Exception("FIXME: Unknown error");
    } /* }}} */
    protected function _runCommand($dbname, array $cmd, ReadPreference $rp = null) { /* {{{ */
        //var_dump(\BSON\toJSON(\BSON\fromArray($cmd)));
        $command = new Command($cmd);
        return $this->manager->executeCommand($dbname, $command, $rp);
    }

    function getCollectionName() {
        return $this->collname;
    }
    function getDatabaseName() {
        return $this->dbname;
    }
}

