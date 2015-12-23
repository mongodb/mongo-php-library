<?php

namespace MongoDB\Operation;

use MongoDB\Driver\Query;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\InvalidArgumentTypeException;
use MongoDB\Exception\RuntimeException;
use MongoDB\Exception\UnexpectedValueException;

/**
 * Operation for the find command.
 *
 * @api
 * @see MongoDB\Collection::find()
 * @see http://docs.mongodb.org/manual/tutorial/query-documents/
 * @see http://docs.mongodb.org/manual/reference/operator/query-modifier/
 */
class Find implements Executable
{
    const NON_TAILABLE = 1;
    const TAILABLE = 2;
    const TAILABLE_AWAIT = 3;

    private $databaseName;
    private $collectionName;
    private $filter;
    private $options;

    /**
     * Constructs a find command.
     *
     * Supported options:
     *
     *  * allowPartialResults (boolean): Get partial results from a mongos if
     *    some shards are inaccessible (instead of throwing an error).
     *
     *  * batchSize (integer): The number of documents to return per batch.
     *
     *  * comment (string): Attaches a comment to the query. If "$comment" also
     *    exists in the modifiers document, this option will take precedence.
     *
     *  * cursorType (enum): Indicates the type of cursor to use. Must be either
     *    NON_TAILABLE, TAILABLE, or TAILABLE_AWAIT. The default is
     *    NON_TAILABLE.
     *
     *  * limit (integer): The maximum number of documents to return.
     *
     *  * maxTimeMS (integer): The maximum amount of time to allow the query to
     *    run. If "$maxTimeMS" also exists in the modifiers document, this
     *    option will take precedence.
     *
     *  * modifiers (document): Meta-operators modifying the output or behavior
     *    of a query.
     *
     *  * noCursorTimeout (boolean): The server normally times out idle cursors
     *    after an inactivity period (10 minutes) to prevent excess memory use.
     *    Set this option to prevent that.
     *
     *  * oplogReplay (boolean): Internal replication use only. The driver
     *    should not set this.
     *
     *  * projection (document): Limits the fields to return for the matching
     *    document.
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): Read concern.
     *
     *    For servers < 3.2, this option is ignored as read concern is not
     *    available.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * skip (integer): The number of documents to skip before returning.
     *
     *  * sort (document): The order in which to return matching documents. If
     *    "$orderby" also exists in the modifiers document, this option will
     *    take precedence.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be
     *    applied to the returned Cursor (it is not sent to the server).
     *
     * @param string       $databaseName   Database name
     * @param string       $collectionName Collection name
     * @param array|object $filter         Query by which to filter documents
     * @param array        $options        Command options
     * @throws InvalidArgumentException
     */
    public function __construct($databaseName, $collectionName, $filter, array $options = [])
    {
        if ( ! is_array($filter) && ! is_object($filter)) {
            throw new InvalidArgumentTypeException('$filter', $filter, 'array or object');
        }

        if (isset($options['allowPartialResults']) && ! is_bool($options['allowPartialResults'])) {
            throw new InvalidArgumentTypeException('"allowPartialResults" option', $options['allowPartialResults'], 'boolean');
        }

        if (isset($options['batchSize']) && ! is_integer($options['batchSize'])) {
            throw new InvalidArgumentTypeException('"batchSize" option', $options['batchSize'], 'integer');
        }

        if (isset($options['comment']) && ! is_string($options['comment'])) {
            throw new InvalidArgumentTypeException('"comment" option', $options['comment'], 'comment');
        }

        if (isset($options['cursorType'])) {
            if ( ! is_integer($options['cursorType'])) {
                throw new InvalidArgumentTypeException('"cursorType" option', $options['cursorType'], 'integer');
            }

            if ($options['cursorType'] !== self::NON_TAILABLE &&
                $options['cursorType'] !== self::TAILABLE &&
                $options['cursorType'] !== self::TAILABLE_AWAIT) {
                throw new InvalidArgumentException('Invalid value for "cursorType" option: ' . $options['cursorType']);
            }
        }

        if (isset($options['limit']) && ! is_integer($options['limit'])) {
            throw new InvalidArgumentTypeException('"limit" option', $options['limit'], 'integer');
        }

        if (isset($options['maxTimeMS']) && ! is_integer($options['maxTimeMS'])) {
            throw new InvalidArgumentTypeException('"maxTimeMS" option', $options['maxTimeMS'], 'integer');
        }

        if (isset($options['modifiers']) && ! is_array($options['modifiers']) && ! is_object($options['modifiers'])) {
            throw new InvalidArgumentTypeException('"modifiers" option', $options['modifiers'], 'array or object');
        }

        if (isset($options['noCursorTimeout']) && ! is_bool($options['noCursorTimeout'])) {
            throw new InvalidArgumentTypeException('"noCursorTimeout" option', $options['noCursorTimeout'], 'boolean');
        }

        if (isset($options['oplogReplay']) && ! is_bool($options['oplogReplay'])) {
            throw new InvalidArgumentTypeException('"oplogReplay" option', $options['oplogReplay'], 'boolean');
        }

        if (isset($options['projection']) && ! is_array($options['projection']) && ! is_object($options['projection'])) {
            throw new InvalidArgumentTypeException('"projection" option', $options['projection'], 'array or object');
        }

        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw new InvalidArgumentTypeException('"readConcern" option', $options['readConcern'], 'MongoDB\Driver\ReadConcern');
        }

        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw new InvalidArgumentTypeException('"readPreference" option', $options['readPreference'], 'MongoDB\Driver\ReadPreference');
        }

        if (isset($options['skip']) && ! is_integer($options['skip'])) {
            throw new InvalidArgumentTypeException('"skip" option', $options['skip'], 'integer');
        }

        if (isset($options['sort']) && ! is_array($options['sort']) && ! is_object($options['sort'])) {
            throw new InvalidArgumentTypeException('"sort" option', $options['sort'], 'array or object');
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw new InvalidArgumentTypeException('"typeMap" option', $options['typeMap'], 'array');
        }

        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->filter = $filter;
        $this->options = $options;
    }

    /**
     * Execute the operation.
     *
     * @see Executable::execute()
     * @param Server $server
     * @return Cursor
     */
    public function execute(Server $server)
    {
        $readPreference = isset($this->options['readPreference']) ? $this->options['readPreference'] : null;

        $cursor = $server->executeQuery($this->databaseName . '.' . $this->collectionName, $this->createQuery(), $readPreference);

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return $cursor;
    }

    /**
     * Create the find query.
     *
     * @return Query
     */
    private function createQuery()
    {
        $options = [];

        if ( ! empty($this->options['allowPartialResults'])) {
            $options['partial'] = true;
        }

        if (isset($options['cursorType'])) {
            if ($options['cursorType'] === self::TAILABLE) {
                $options['tailable'] = true;
            }
            if ($options['cursorType'] === self::TAILABLE_AWAIT) {
                $options['tailable'] = true;
                $options['awaitData'] = true;
            }
        }

        foreach (['batchSize', 'limit', 'skip', 'sort', 'noCursorTimeout', 'oplogReplay', 'projection', 'readConcern'] as $option) {
            if (isset($this->options[$option])) {
                $options[$option] = $this->options[$option];
            }
        }

        $modifiers = empty($this->options['modifiers']) ? [] : (array) $this->options['modifiers'];

        if (isset($options['comment'])) {
            $modifiers['$comment'] = $options['comment'];
        }

        if (isset($options['maxTimeMS'])) {
            $modifiers['$maxTimeMS'] = $options['maxTimeMS'];
        }

        if ( ! empty($modifiers)) {
            $options['modifiers'] = $modifiers;
        }

        return new Query($this->filter, $options);
    }
}
