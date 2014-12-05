<?php
namespace MongoDB;

use MongoDB\Manager;
use MongoDB\Query;
use MongoDB\ReadPreference;
use MongoDB\WriteBatch;

class QueryFlags {
    const TAILABLE_CURSOR   = 0x02;
    const SLAVE_OKAY        = 0x04;
    const OPLOG_REPLY       = 0x08;
    const NO_CURSOR_TIMEOUT = 0x10;
    const AWAIT_DATA        = 0x20;
    const EXHAUST           = 0x40;
    const PARTIAL           = 0x80;
}
class CursorType {
    const NON_TAILABLE   = 0x00;
    const TAILABLE       = QueryFlags::TAILABLE_CURSOR;
    /* QueryFlags::TAILABLE_CURSOR | QueryFlags::AWAIT_DATA; */
    const TAILABLE_AWAIT = 0x22;
}

class Collection {
    const INSERT = 0x01;
    const UPDATE = 0x02;
    const DELETE = 0x04;

    protected $manager;
    protected $rp;
    protected $wc;
    protected $ns;

    function __construct(Manager $manager, $ns, WriteConcern $wc = null, ReadPreference $rp = null) {
        $this->manager = $manager;
        $this->ns = $ns;
        $this->wc = $wc;
        $this->rp = $rp;
    }

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
             * The default is NON_TAILABLE.
             *
             * @see http://docs.mongodb.org/manual/reference/operator/meta/comment/
             */
            "cursorType" => CursorType::NON_TAILABLE,

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

        $flags |= $options["allowPartialResults"] ? QueryFlags::PARTIAL : 0;
        $flags |= $options["cursorType"] ? $options["cursorType"] : 0;
        $flags |= $options["oplogReplay"] ? QueryFlags::OPLOG_REPLY: 0;
        $flags |= $options["noCursorTimeout"] ? QueryFlags::NO_CURSOR_TIMEOUT : 0;

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

    protected function _writeSingle($filter, $type, array $options = array(), $newobj = array()) { /* {{{ */
        $options = array_merge($this->getWriteOptions(), $options);

        $batch  = new WriteBatch($options["ordered"]);
        switch($type) {
            case self::INSERT:
                $batch->insert($filter);
                break;

            case self::DELETE:
                $batch->delete($filter, $options);
                break;

            case self::UPDATE:
                $batch->update($filter, $newobj, $options);
                break;
        }

        return $this->manager->executeWriteBatch($this->ns, $batch, $this->wc);
    } /* }}} */
    function getWriteOptions() { /* {{{ */
        return array(
            "ordered" => false,
            "upsert"  => false,
            "limit"   => 1,
        );
    } /* }}} */

    function insertOne(array $filter) { /* {{{ */
        return $this->_writeSingle($filter, self::INSERT);
    } /* }}} */
}

